<div class="empty-state" role="status" aria-live="polite">
    <span class="empty-state__mark" aria-hidden="true">M30</span>
    <div>
        <strong>{{ $title }}</strong>
        @isset($message)
            <p>{{ $message }}</p>
        @endisset
    </div>
</div>
