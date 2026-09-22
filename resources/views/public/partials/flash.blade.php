@if (session('success') || session('status') || session('error'))
    <div @class(['flash-stack', 'flash-stack--account' => request()->routeIs('account.*')])>
        @if (session('success'))
            <div class="flash-banner flash-banner--success" role="status">{{ session('success') }}</div>
        @endif

        @if (session('status'))
            <div class="flash-banner flash-banner--status" role="status">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="flash-banner flash-banner--error" role="alert">{{ session('error') }}</div>
        @endif
    </div>
@endif
