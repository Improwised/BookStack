
    <div class="mt-xxs px-s pt-s card hidden"  style="background-color:#fff;position:absolute;width:100%;z-index:999;"  refs="tags-suggestions@suggestionBox">
        
        <div class="tags-suggestion  editor-content-wrap" refs="tags-suggestions@tags" style="max-height: 210px;">
            @foreach ($tags as $tag)
            <?php
                $value = array_search(strtolower($tag->name), array_map('strtolower', $currentList));
             ?>
            <div refs="tags-suggestions@suggestionsTagModel" class="entity-list-item p-xxs tag {{$value !== false?'hidden':''}}">
                <label  id="{{$tag->name}}" class="toggle-switch">
                    <input type="hidden" name="tags-suggestions" value="{{$value !== false ?'true':'false'}}"/>
                    <input type="checkbox" name="tag-sugesstion-checkbox" data-id="{{$tag->name}}" @if($value !== false ) checked="checked" @endif>
                    <span tabindex="0" role="checkbox"
                        aria-checked="{{ $value !== false  ? 'true' : 'false' }}"
                        class="custom-checkbox text-primary">@icon('check')</span>
                    <span class="label">{{ $tag->name }}</span>
                </label>
            </div>
            @endforeach
        </div>
    </div>