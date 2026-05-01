{{-- Stat card putih: <x-stat-card label="Total Mahasiswa" :value="$total" link="route.name" link-text="Kelola" /> --}}
@props([
    'label'    => '',
    'value'    => 0,
    'sub'      => null,
    'icon'     => null,
    'link'     => null,
    'linkText' => 'Lihat →',
    'navy'     => false,
])

<div style="background:{{ $navy ? 'linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%)' : 'white' }};border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);{{ $navy ? 'color:white;' : '' }}">
    @if($icon)
    <div style="font-size:20px;margin-bottom:8px;{{ $navy ? 'opacity:.7;' : 'color:#6B7489;' }}"><i class="bi bi-{{ $icon }}"></i></div>
    @endif
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;{{ $navy ? 'opacity:.7;' : 'color:#6B7489;' }}">{{ $label }}</div>
    <div style="font-size:28px;font-weight:700;{{ $navy ? 'color:white;' : 'color:#0B1E4F;' }}">{{ $value }}</div>
    @if($sub)
    <div style="font-size:12px;margin-top:4px;{{ $navy ? 'opacity:.6;' : 'color:#6B7489;' }}">{{ $sub }}</div>
    @endif
    @if($link)
    <a href="{{ route($link) }}" style="font-size:12px;text-decoration:none;margin-top:8px;display:inline-block;{{ $navy ? 'color:rgba(255,255,255,.8);' : 'color:#2A4A9E;' }}">{{ $linkText }}</a>
    @endif
    {{ $slot }}
</div>
