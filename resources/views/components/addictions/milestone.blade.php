<div class="milestone">
    <form class="milestone" action="{{ route('addiction.milestone.destroy', ['milestone' => $milestone->uuid]) }}" method="POST">
        @csrf

        <p>{{ $milestone->name }}</p>

        {{-- Trashcan icon/delete submit button --}}
        <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
        <button class="delete" type="submit">Delete</button>
    </form>
</div>
@if(!is_null($milestone->reward))
    <div class="reward">
        <p>Reward: {{ $milestone->reward }}</p>
    </div>
@endif