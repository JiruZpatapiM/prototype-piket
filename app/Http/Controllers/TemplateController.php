<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    public function addItem(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'section_index' => 'required|integer',
            'subsection_index' => 'required|integer',
            'item_name' => 'required|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $content = $template->content;

        if (isset($content[$request->section_index]['subsections'][$request->subsection_index])) {
            $content[$request->section_index]['subsections'][$request->subsection_index]['items'][] = $request->item_name;
            $template->content = $content;
            $template->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid section or subsection']);
    }

    public function deleteItem(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'section_index' => 'required|integer',
            'subsection_index' => 'required|integer',
            'item_index' => 'required|integer',
        ]);

        $template = Template::findOrFail($request->template_id);
        $content = $template->content;

        if (isset($content[$request->section_index]['subsections'][$request->subsection_index]['items'][$request->item_index])) {
            unset($content[$request->section_index]['subsections'][$request->subsection_index]['items'][$request->item_index]);
            // Re-index array
            $content[$request->section_index]['subsections'][$request->subsection_index]['items'] = array_values($content[$request->section_index]['subsections'][$request->subsection_index]['items']);
            
            $template->content = $content;
            $template->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Item not found']);
    }
}
