<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PiketInput;
use App\Models\PiketDetail;
use Illuminate\Support\Facades\Auth;

class PiketController extends Controller
{
    public function create(Request $request)
    {
        $jenis_piket = $request->query('jenis_piket', 'Daily');
        $lokasi = $request->query('lokasi', null);
        
        $query = PiketInput::orderBy('created_at', 'desc');
        
        if (Auth::check() && Auth::user()->role !== 'admin') {
            // Check if already submitted today
            $alreadySubmitted = PiketInput::where('user_id', Auth::id())
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->where('status', 'submitted')
                ->exists();
                
            if ($alreadySubmitted) {
                return redirect()->route('piket.history')->with('error', 'Anda sudah mengirim laporan hari ini. Silakan kembali besok setelah jam 00:00.');
            }
            
            $query->where('user_id', Auth::id());
        } else {
            // Admin filtering logic
            if ($lokasi) {
                $query->where('lokasi', $lokasi);
            }
        }
        
        $history = $query->paginate(10);
        $template = null;
        if ($jenis_piket) {
            $template = \App\Models\Template::where('jenis_piket', $jenis_piket)->first();
            
            // Auto-seed Libur Nataru if missing, by copying Angkutan Lebaran
            if (!$template && $jenis_piket === 'Libur Nataru') {
                $angkutan = \App\Models\Template::where('jenis_piket', 'Angkutan Lebaran')->first();
                if ($angkutan) {
                    $template = \App\Models\Template::create([
                        'jenis_piket' => 'Libur Nataru',
                        'content' => $angkutan->content
                    ]);
                }
            }
        }
        
        return view('piket.input', compact('jenis_piket', 'history', 'template', 'lokasi'));
    }

    public function store(Request $request)
    {
        // Pastikan kolom file_path bertipe TEXT agar bisa menampung banyak file
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE piket_inputs MODIFY file_path TEXT');
        } catch (\Exception $e) {}

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'jenis_piket' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'nullable|array',
            'lampiran' => 'nullable|array|max:10', // Max 10 files
            'lampiran.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB limit per file
        ]);

        $filePaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                // Generate a short filename to stay within varchar(255) when JSON encoded
                $path = $file->storeAs('lampiran_piket', substr(uniqid(), -8) . '.' . $file->extension(), 'public');
                $filePaths[] = $path;
            }
        }
        $filePath = count($filePaths) > 0 ? json_encode($filePaths) : null;

        $isDraft = $request->input('action') === 'draft';
        
        if (!$isDraft && \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            $alreadySubmitted = PiketInput::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->where('status', 'submitted')
                ->exists();
                
            if ($alreadySubmitted) {
                return redirect()->route('piket.history')->with('error', 'Anda hanya dapat mengirim laporan (Submit) 1 kali per hari. Laporan Anda untuk hari ini sudah terkirim.');
            }
        }
        
        // Calculate percentages and scores
        $template = \App\Models\Template::where('jenis_piket', $validated['jenis_piket'])->first();
        $totalItems = 0;
        if ($template) {
            foreach ($template->content as $section) {
                foreach ($section['subsections'] as $sub) {
                    if (isset($sub['items'])) {
                        $totalItems += count($sub['items']);
                    }
                }
            }
        }

        $answeredItems = 0;
        $baikCount = 0;
        $scoredItemsCount = 0;
        $itemsData = $request->input('items', []);
        
        foreach ($itemsData as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $items) {
                foreach ($items as $item_name => $data) {
                    if ($item_name === '_catatan_') continue;
                    
                    $kondisi = $data['kondisi'] ?? $data['uraian'] ?? null;
                    if ($kondisi !== null && trim($kondisi) !== '') {
                        $answeredItems++;
                        if (isset($data['kondisi'])) {
                            $scoredItemsCount++;
                            if (strtolower($data['kondisi']) === 'baik') {
                                $baikCount++;
                            }
                        }
                    }
                }
            }
        }
        
        $persentase = $totalItems > 0 ? round(($answeredItems / $totalItems) * 100) : 0;
        $score = $scoredItemsCount > 0 ? round(($baikCount / $scoredItemsCount) * 100) : 0;
        $status = $isDraft ? 'draft' : 'submitted';

        $input = PiketInput::create([
            'user_id' => Auth::id() ?? 1,
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'],
            'jenis_piket' => $validated['jenis_piket'],
            'persentase' => $persentase,
            'status' => $status,
            'score' => $score,
            'catatan' => $validated['catatan'] ?? null,
            'file_path' => $filePath,
        ]);

        foreach ($itemsData as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $items) {
                foreach ($items as $item_name => $data) {
                    PiketDetail::create([
                        'piket_input_id' => $input->id,
                        'category' => $category,
                        'subcategory' => $subcategory,
                        'item_name' => $item_name,
                        'kondisi' => $data['kondisi'] ?? $data['uraian'] ?? null,
                        'metode' => $data['metode'] ?? null,
                    ]);
                }
            }
        }

        $msg = $isDraft ? 'Draft Laporan Piket berhasil disimpan sementara!' : 'Data Monitoring Piket berhasil dikirim!';
        return redirect()->route('piket.input', ['jenis_piket' => $validated['jenis_piket']])->with('success', $msg)->withFragment('history-section');
    }

    public function edit($id)
    {
        $draft = PiketInput::with('details')->findOrFail($id);
        
        // Ensure RBAC for edit
        if (Auth::user()->role !== 'admin' && $draft->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($draft->status !== 'draft') {
            return redirect()->route('piket.history')->with('error', 'Hanya data draft yang dapat diedit.');
        }

        if (Auth::check() && Auth::user()->role !== 'admin') {
            $alreadySubmitted = PiketInput::where('user_id', Auth::id())
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->where('status', 'submitted')
                ->exists();
                
            if ($alreadySubmitted) {
                return redirect()->route('piket.history')->with('error', 'Anda sudah mengirim laporan hari ini. Silakan kembali besok setelah jam 00:00.');
            }
        }

        $jenis_piket = $draft->jenis_piket;
        $lokasi = $draft->lokasi;
        $template = \App\Models\Template::where('jenis_piket', $jenis_piket)->first();
        
        // History for the same view
        $query = PiketInput::orderBy('created_at', 'desc');
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }
        $history = $query->paginate(10);
        
        return view('piket.input', compact('jenis_piket', 'history', 'template', 'lokasi', 'draft'));
    }

    public function update(Request $request, $id)
    {
        // Pastikan kolom file_path bertipe TEXT agar bisa menampung banyak file
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE piket_inputs MODIFY file_path TEXT');
        } catch (\Exception $e) {}

        $input = PiketInput::findOrFail($id);
        
        // Ensure RBAC for update
        if (Auth::user()->role !== 'admin' && $input->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($input->status !== 'draft') {
            return redirect()->route('piket.history')->with('error', 'Hanya data draft yang dapat diedit.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'jenis_piket' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'nullable|array',
            'lampiran' => 'nullable|array|max:10',
            'lampiran.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        // Keep existing files if any
        $filePaths = [];
        if ($input->file_path) {
            $decoded = json_decode($input->file_path, true);
            $filePaths = is_array($decoded) ? $decoded : [$input->file_path];
        }

        if ($request->hasFile('lampiran')) {
            // Delete old files if uploading new ones (optional, let's just append or replace? Let's replace for simplicity)
            foreach ($filePaths as $oldPath) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            $filePaths = [];
            foreach ($request->file('lampiran') as $file) {
                $path = $file->storeAs('lampiran_piket', substr(uniqid(), -8) . '.' . $file->extension(), 'public');
                $filePaths[] = $path;
            }
        }
        $filePath = count($filePaths) > 0 ? json_encode($filePaths) : null;

        $isDraft = $request->input('action') === 'draft';
        
        if (!$isDraft && \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            $alreadySubmitted = PiketInput::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->where('status', 'submitted')
                ->exists();
                
            if ($alreadySubmitted) {
                return redirect()->route('piket.history')->with('error', 'Anda hanya dapat mengirim laporan (Submit) 1 kali per hari. Laporan Anda untuk hari ini sudah terkirim.');
            }
        }
        
        // Calculate percentages and scores
        $template = \App\Models\Template::where('jenis_piket', $validated['jenis_piket'])->first();
        $totalItems = 0;
        if ($template) {
            foreach ($template->content as $section) {
                foreach ($section['subsections'] as $sub) {
                    if (isset($sub['items'])) {
                        $totalItems += count($sub['items']);
                    }
                }
            }
        }

        $answeredItems = 0;
        $baikCount = 0;
        $scoredItemsCount = 0;
        $itemsData = $request->input('items', []);
        
        foreach ($itemsData as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $items) {
                foreach ($items as $item_name => $data) {
                    if ($item_name === '_catatan_') continue;
                    
                    $kondisi = $data['kondisi'] ?? $data['uraian'] ?? null;
                    if ($kondisi !== null && trim($kondisi) !== '') {
                        $answeredItems++;
                        if (isset($data['kondisi'])) {
                            $scoredItemsCount++;
                            if (strtolower($data['kondisi']) === 'baik') {
                                $baikCount++;
                            }
                        }
                    }
                }
            }
        }
        
        $persentase = $totalItems > 0 ? round(($answeredItems / $totalItems) * 100) : 0;
        $score = $scoredItemsCount > 0 ? round(($baikCount / $scoredItemsCount) * 100) : 0;
        $status = $isDraft ? 'draft' : 'submitted';

        $input->update([
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'],
            'jenis_piket' => $validated['jenis_piket'],
            'persentase' => $persentase,
            'status' => $status,
            'score' => $score,
            'catatan' => $validated['catatan'] ?? null,
            'file_path' => $filePath,
        ]);

        // Delete old details
        $input->details()->delete();

        // Insert new details
        foreach ($itemsData as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $items) {
                foreach ($items as $item_name => $data) {
                    PiketDetail::create([
                        'piket_input_id' => $input->id,
                        'category' => $category,
                        'subcategory' => $subcategory,
                        'item_name' => $item_name,
                        'kondisi' => $data['kondisi'] ?? $data['uraian'] ?? null,
                        'metode' => $data['metode'] ?? null,
                    ]);
                }
            }
        }

        $msg = $isDraft ? 'Draft Laporan Piket berhasil diupdate!' : 'Data Monitoring Piket berhasil dikirim!';
        return redirect()->route('piket.input', ['jenis_piket' => $validated['jenis_piket']])->with('success', $msg)->withFragment('history-section');
    }

    public function exportPdf(Request $request, $id)
    {
        $input = \App\Models\PiketInput::with('details')->findOrFail($id);
        
        // Ensure RBAC for download
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin' && $input->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $pdf = app('dompdf.wrapper')->loadView('piket.pdf_export', compact('input'));
        $fileName = 'Laporan_Piket_' . str_replace(' ', '_', $input->jenis_piket) . '_' . $input->tanggal . '.pdf';
        
        return $pdf->download($fileName);
    }

    public function downloadLampiran($id)
    {
        $input = \App\Models\PiketInput::findOrFail($id);
        
        // Ensure RBAC for download
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin' && $input->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!$input->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($input->file_path)) {
            return back()->with('error', 'File lampiran tidak ditemukan.');
        }

        return response()->download(storage_path('app/public/' . $input->file_path));
    }

    public function history(Request $request)
    {
        $query = PiketInput::query();
        
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lokasi', 'like', "%{$search}%")
                  ->orWhere('jenis_piket', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $history = $query->orderBy('tanggal', 'desc')->paginate(10);
        return view('piket.history', compact('history'));
    }

    public function laporan(Request $request)
    {
        $query = PiketInput::whereNotNull('file_path')->where('file_path', '!=', '');
        
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lokasi', 'like', "%{$search}%")
                  ->orWhere('jenis_piket', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $history = $query->orderBy('tanggal', 'desc')->paginate(10);
        return view('piket.laporan', compact('history'));
    }

    public function downloadTemplate(Request $request)
    {
        $type = $request->query('type', 'angkutan');

        if ($type === 'angkutan') {
            $service = new \App\Services\ExcelTemplateService();
            $spreadsheet = $service->generateTemplate('Angkutan Lebaran');

            $fileName = 'Template_Monitoring_Angkutan_Lebaran.xlsx';
            $tempFile = storage_path('app/public/' . $fileName);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($tempFile);

            return response()->download($tempFile)->deleteFileAfterSend(true);
        } elseif ($type === 'daily') {
            $service = new \App\Services\ExcelTemplateService();
            $spreadsheet = $service->generateTemplate('Daily');

            $fileName = 'Template_Monitoring_Daily_Weekend.xlsx';
            $tempFile = storage_path('app/public/' . $fileName);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($tempFile);

            return response()->download($tempFile)->deleteFileAfterSend(true);
        }

        // Mock response for other types
        return response("Template $type is not yet implemented in rich format.", 404);
    }
}
