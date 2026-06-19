@extends('layouts.app')

@section('content')
<div class="login-wrapper" style="background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="card login-card" style="background: #ffffff; width: 100%; max-width: 440px; box-shadow: 0 30px 60px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02); text-align: center; border: none; border-radius: 24px; padding: 3.5rem 3rem; position: relative; overflow: hidden;">
        
        <!-- Subtle Background Decoration -->
        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(14,165,233,0.1) 0%, rgba(14,165,233,0) 70%); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -50px; left: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(2,132,199,0.05) 0%, rgba(2,132,199,0) 70%); border-radius: 50%;"></div>

        <div style="display: flex; justify-content: center; margin-bottom: 2rem; position: relative; z-index: 1;">
            <!-- Pelindo Official Logo -->
            <img src="{{ asset('logo/Logo_Baru_Pelindo.png') }}" alt="Logo Pelindo" style="max-height: 55px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));">
        </div>

        <div style="margin-bottom: 2.5rem; position: relative; z-index: 1;">
            <h2 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin-bottom: 0.3rem; letter-spacing: -0.5px;">Monitoring Piket</h2>
            <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">PT Pelindo Regional 4</p>
        </div>

        @if($errors->any())
            <div class="alert text-sm" style="background-color: #fff1f2; color: #e11d48; border-left: 4px solid #e11d48; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: left; font-weight: 500;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="text-align: left; position: relative; z-index: 1;">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="color: #475569; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; letter-spacing: 0.5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; color: #0ea5e9;">
                        <circle cx="12" cy="7" r="4"></circle><path d="M5.5 21v-2a4 4 0 0 1 4-4h5a4 4 0 0 1 4 4v2"></path>
                    </svg>
                    USERNAME / NPP
                </label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Masukkan NPP Anda" required style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; color: #0f172a; font-size: 1rem; font-weight: 600; padding: 0.85rem 1rem; border-radius: 10px; width: 100%; transition: all 0.2s ease; outline: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" onfocus="this.style.borderColor='#0ea5e9'; this.style.backgroundColor='#ffffff'; this.style.boxShadow='0 0 0 4px rgba(14,165,233,0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="color: #475569; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; letter-spacing: 0.5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; color: #0ea5e9;">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    PASSWORD
                </label>
                <div style="position: relative;">
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; color: #0f172a; font-size: 1rem; font-weight: 600; padding: 0.85rem 1rem; border-radius: 10px; width: 100%; letter-spacing: 2px; transition: all 0.2s ease; outline: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" onfocus="this.style.borderColor='#0ea5e9'; this.style.backgroundColor='#ffffff'; this.style.boxShadow='0 0 0 4px rgba(14,165,233,0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                </div>
            </div>

            <div class="flex justify-between items-center mb-8" style="margin-top: 1rem; margin-bottom: 2rem;">
                <label class="text-sm" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #64748b; font-weight: 500;">
                    <input type="checkbox" name="remember" style="accent-color: #0ea5e9; width: 16px; height: 16px; border-radius: 4px; cursor: pointer;">
                    Ingat saya
                </label>
                <a href="#" class="text-sm" style="color: #0ea5e9; font-weight: 700; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#0ea5e9'">Lupa Password?</a>
            </div>

            <button type="submit" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; font-size: 1.05rem; font-weight: 700; padding: 1rem; border-radius: 12px; border: none; width: 100%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 20px -10px rgba(14,165,233,0.6);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 25px -10px rgba(14,165,233,0.8)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 20px -10px rgba(14,165,233,0.6)';">
                MASUK KE SISTEM
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 8px;">
                    <line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection
