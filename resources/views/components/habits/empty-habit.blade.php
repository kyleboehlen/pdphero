<div class="habit">
    {{-- Habit title/link --}}
    <a href="{{ route('habits.create') }}">
        <h2>Create Your First Habit</h2>
    </a>

    {{-- Habit progress bar --}}
    <div class="progress-container">
        <div class="progress-bar no-border">
            <span>0%</span>
        </div>
    </div><br/>

    {{-- Habit history tracker/toggle --}}
    <div class="history-toggle-container">
        @for($i = 0; $i < 7; $i++)
            <div class="history-toggle-item">
                <p>???</p>
                <div class="toggle-history"></div>
            </div>
        @endfor
    </div>
</div>