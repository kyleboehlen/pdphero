<form class="bucketlist" method="POST"
    @isset($item)
        action="{{ route('bucketlist.update', ['bucketlist_item' => $item->uuid]) }}"
    @else
        action="{{ route('bucketlist.store') }}"
    @endisset>

    @csrf

    {{-- Header --}}
    <h2>@isset($item) Edit Item @else Create New Item @endisset</h2>

    <input type="text" name="name" placeholder="Bucketlist Item" maxlength="255" 
        @isset($item)
            value="{{ $item->name }}"
        @else
            value="{{ old('name') }}" 
        @endisset required />

    @error('name')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    <select name="category" required>
        <option @if(is_null($item) && is_null(old('category'))) selected @endif value="no-category">No Category</option>

        @foreach($categories as $category)
            <option value="{{ $category->uuid }}"
                @if(!is_null($item) && $item->category_id == $category->id)
                    selected
                @elseif(old('category') == $category->uuid)
                    selected
                @endif
            >{{ $category->name }}</option>
        @endforeach
    </select>

    @error('category')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    <textarea name="details" placeholder="Any extra details for your bucketlist item go here!"
        >@isset($item){{ $item->notes }}@else{{ old('details') }}@endisset</textarea>

    @error('details')
        <p class="error">{{ $message }}</p>
    @enderror

    <a href="{{ route('bucketlist') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    