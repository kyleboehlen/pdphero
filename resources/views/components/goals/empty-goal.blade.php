<div class="goal empty">
    {{-- Goal image --}}
    <img class="goal-img" src="{{ asset('icons/goal-status-tbd-black.png') }}" />

    {{-- Goal title/link --}}
    <a href="{{ route('goals.create.goal') }}">
        <h2>Create A Goal</h2>
    </a>

    {{-- Goal progress bar --}}
    <x-app.progress-bar percent="0" /><br/>

    {{-- Status label/bar --}}
    <div class="status" title="This goal needs to be created!">
        <p class="label tbd">To Be Determined</p>
        <img src="{{ asset('icons/goal-status-bar-tbd.png') }}" />
    </div>
</div>