<div class="addiction">
    {{-- Method image --}}
    @if($addiction->method_id == $method::ABSTINENCE)
        <img class="method-img" src="{{ asset('icons/addiction-milestone.png') }}" />
    @elseif($addiction->method_id == $method::MODERATION)
        <img class="method-img" src="{{ asset('icons/addiction-usage.png') }}" />
    @endif

    {{-- Title/link --}}
    <div class="info">
        <a href="{{ route('addiction.details', ['addiction' => $addiction->uuid]) }}">
            <h2>{{ $addiction->name }}</h2>
        </a>

        {{-- Milestone/usage info --}}
        @if($addiction->method_id == $method::ABSTINENCE)
            <span class="milestone">
                <p class="{{ is_null($milestone_name) ? 'yellow' : 'green' }}">{{ $milestone_name ?? 'Pending Milestone'}}</p>
            </span>
        @elseif($addiction->method_id == $method::MODERATION)
            <span class="usage">
                <p><span class="color {{ $usage_color }}"> {{ $usage }}</span> / {{ $addiction->moderated_amount }}</p>
            </span>
        @endif

        {{-- Elapsed time --}}
        <x-addictions.elapsed-timer :addiction="$addiction" />
    </div>
</div>