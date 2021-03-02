<div class="habit">
    {{-- Habit title/link --}}
    <a href="{{ route('habits.view', ['habit' => $habit->uuid]) }}">
        <h2>{{ $habit->name }}</h2>
    </a>

    {{-- Habit progress bar --}}
    <x-app.progress-bar :percent="$habit->strength" /><br/>

    {{-- Habit history tracker/toggle --}}
    <x-habits.history :habit="$habit" />
</div>