{{-- Flash alert: <x-alert type="success" :message="session('success')" /> --}}
@props(['type' => 'success', 'message' => null])

@if($message)
@php
$styles = match($type) {
    'success' => 'background:#D1FAE5;color:#065F46;border-left:4px solid #10B981;',
    'error'   => 'background:#FEE2E2;color:#991B1B;border-left:4px solid #EF4444;',
    'warning' => 'background:#FEF3C7;color:#92400E;border-left:4px solid #F59E0B;',
    'info'    => 'background:#DBEAFE;color:#1E40AF;border-left:4px solid #3B82F6;',
    default   => 'background:#F3F4F6;color:#374151;border-left:4px solid #9CA3AF;',
};
$icons = match($type) {
    'success' => 'check-circle',
    'error'   => 'x-circle',
    'warning' => 'exclamation-triangle',
    'info'    => 'info-circle',
    default   => 'info-circle',
};
@endphp
<div style="padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;display:flex;align-items:center;gap:10px;{{ $styles }}">
    <i class="bi bi-{{ $icons }}"></i>
    {{ $message }}
</div>
@endif
