{{--
@type - Type of term (exact, tag)
@currentList
--}}
<div component="add-remove-rows"
       option:add-remove-rows:remove-selector="button.text-neg"
       option:add-remove-rows:row-selector=".flex-container-row"
        class="flex-container-column gap-xs">
        @foreach(array_merge($currentList, ['']) as $term)
            <div @if(empty($term)) refs="add-remove-rows@model" @endif
                class="{{ $term ? '' : 'hidden' }} flex-container-row items-center gap-x-xs">
                <div>
                @if($type === 'tags')
                <div component="tags-suggestions"
                    option:tags-suggestions:all-tags="{{json_encode($tags)}}"
                    option:tags-suggestions:selected-tags="{{json_encode($currentList)}}" style="position: relative;">
                @endif 
                    <input class="exact-input outline" type="text" autocomplete="off" {{ $type === 'tags' ? 'refs=tags-suggestions@input' : '' }} name="{{$type}}[]" value="{{ $term }}">
                    @if ($type == 'tags' ) @include('search.parts.auto-complete',['tags'=>$tags]) @endif 
                @if($type === 'tags')
                </div>    
                @endif
                </div>
                <div>
                    <button type="button" class="text-neg text-button icon-button p-xs">@icon('close')</button>
                </div>
            </div>
        @endforeach
        
        <div class="flex py-xs">
            <button refs="add-remove-rows@add" type="button" class="text-button">
                @icon('add-circle'){{ trans('common.add') }}
            </button>
        </div>
    
</div>