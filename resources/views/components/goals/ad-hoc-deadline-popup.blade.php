<div class="overlay-hide ad-hoc-deadline-container" id="ad-hoc-deadline-container-{{ $ad_hoc_item->uuid }}">
    {{-- Goal Header --}}
    <h2>Set {{ $ad_hoc_item->name }} Deadline</h2><br/><br/><br/>
    
    {{-- Set deadline form --}}
    <form
        @if($ad_hoc_item instanceof \App\Models\Bucketlist\BucketlistItem)
            action="{{ route('goals.bucketlist-deadline.set', ['bucketlist_item' => $ad_hoc_item->uuid, 'goal' => $goal->uuid, 'view_details' => $view_details]) }}" method="POST"
        @else
            action="{{ route('goals.ad-hoc-deadline.set', ['action_item' => $ad_hoc_item->uuid, 'view_details' => $view_details]) }}" method="POST"
        @endif>

        @csrf

        <p>Due by:</p>
        <div class="deadline-input-container">
            <input type="date" name="deadline" id="deadline-input" />
        </div>
        @error('deadline')
            <script>
                sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
            </script>
        @enderror
        <br/><br/><br/>

        {{-- Cancel/Save buttons --}}
        <div class="buttons-container">
            <button type="button" class="swal2-confirm swal2-styled manual-progress-updater-button overlay-cancel-button">Cancel</button>
            <button type="submit" class="swal2-confirm swal2-styled manual-progress-updater-button">Save</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#set-deadline-link-{{ $ad_hoc_item->uuid }}').add('#set-deadline-icon-{{ $ad_hoc_item->uuid }}').click(function(){
            $('.overlay').show();
            $('#ad-hoc-deadline-container-{{ $ad_hoc_item->uuid }}').show();
        });
    });
</script>