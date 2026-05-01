{{-- Status badge: <x-badge type="aktif" /> atau <x-badge type="wajib">Wajib</x-badge> --}}
@props(['type' => 'default'])

@php
$styles = match($type) {
    'aktif'     => 'background:#D1FAE5;color:#065F46;',
    'nonaktif'  => 'background:#F3F4F6;color:#6B7280;',
    'cuti'      => 'background:#FEF3C7;color:#92400E;',
    'lulus'     => 'background:#EDE9FE;color:#5B21B6;',
    'wajib'     => 'background:#E3F2FD;color:#1976D2;',
    'pilihan'   => 'background:#F3E5F5;color:#7B1FA2;',
    'academic'  => 'background:#E3F2FD;color:#1565C0;',
    'event'     => 'background:#E8F5E9;color:#2E7D32;',
    'system'    => 'background:#F3E5F5;color:#7B1FA2;',
    'A'         => 'background:#D1FAE5;color:#065F46;',
    'B+'        => 'background:#D1FAE5;color:#065F46;',
    'B'         => 'background:#FEF9C3;color:#854D0E;',
    'C+'        => 'background:#FEF3C7;color:#92400E;',
    'C'         => 'background:#FFEDD5;color:#9A3412;',
    'D'         => 'background:#FEE2E2;color:#991B1B;',
    'E'         => 'background:#FEE2E2;color:#991B1B;',
    default     => 'background:#F3F4F6;color:#374151;',
};
@endphp

<span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;{{ $styles }}">
    {{ $slot->isEmpty() ? ucfirst($type) : $slot }}
</span>
