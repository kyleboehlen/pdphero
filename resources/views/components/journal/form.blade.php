<form class="journal-entry" method="POST"
    @isset($journal_entry)
        action="{{ route('journal.update.entry', ['journal_entry' => $journal_entry->uuid]) }}"
    @else
        action="{{ route('journal.store.entry') }}"
    @endisset>
    @csrf

    {{-- Header --}}
    <h2>@isset($journal_entry) Edit Entry @else Add New Entry @endisset</h2>

    <input type="text" name="title" placeholder="Title" maxlength="255" 
        @isset($journal_entry)
            value="{{ $journal_entry->title }}"
        @else
            value="{{ old('title') }}" 
        @endisset required />
    @error('title')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    <select name="category" required>
        <option @if(is_null($journal_entry) && is_null(old('category'))) selected @endif value="0">Journal Entry</option>

        @foreach($categories as $category)
            <option value="{{ $category->uuid }}"
                @if(!is_null($journal_entry) && $journal_entry->category_id == $category->id)
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

    @isset($journal_entry)
        <x-journal.mood-selector :selected="$journal_entry->mood_id" />
    @else
        <x-journal.mood-selector />
    @endisset

    <br/>
    <textarea name="body" required
        placeholder="Your journal entry goes here! :)"
    >@isset($journal_entry){{ $journal_entry->body }}@else{{ old('body') }}@endisset</textarea><br/><br/>
    @error('body')
        <p class="error">{{ $message }}</p>
    @enderror

    <a href="{{ route('journal.view.list') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    