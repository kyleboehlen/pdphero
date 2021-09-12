<div class="ad-hoc-list-item">
    <a class="set-deadline-icon" id="set-deadline-icon-{{ $ad_hoc_item->uuid }}" href="#">
        <img src="{{ asset('icons/calendar-white.png') }}" />
    </a>
    &nbsp;
    <a
        @if($ad_hoc_item instanceof \App\Models\Bucketlist\BucketlistItem)
            href="{{ route('goals.view.bucketlist-item', ['bucketlist_item' => $ad_hoc_item->uuid, 'goal' => $goal->uuid]) }}"
        @else
            href="{{ route('goals.view.action-item', ['action_item' => $ad_hoc_item->uuid]) }}"
        @endif
        >{{ $ad_hoc_item->name }}
    </a>
    &nbsp;
    <a class="set-deadline-link" id="set-deadline-link-{{ $ad_hoc_item->uuid }}" href="#">
        <div class="deadline">
            <p>Set Deadline</p>
        </div>
    </a>
</div>

@if($ad_hoc_item instanceof \App\Models\Bucketlist\BucketlistItem)
    @push('scripts')
        <x-goals.ad-hoc-deadline-popup :item="$ad_hoc_item" :goal="$goal" />
    @endpush
@else
    @push('scripts')
        <x-goals.ad-hoc-deadline-popup :item="$ad_hoc_item" />
    @endpush
@endif