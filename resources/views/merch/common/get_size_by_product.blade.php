<div class="col-sm-12"><div class="checkbox">
    @if(count($sizeList) > 0) 
        @foreach ($sizeList as $key => $v)
            <label class="col-sm-2" style="padding:0px;">
                <input name='sizeGroups[]' type='checkbox' id='sizeGroups' class='ace' value='{{$key}}'>
                <span class='lbl'> {!!$sizegroupList[$key]!!}</span>;
            @if(count($v) > 0)
            <ul>
                @foreach($v as $k1 =>$size)
                    <li>{{$size}}</li>
                @endforeach
            </ul>
            @endif
            </label>
        @endforeach
    @else
        <div class="row">
            <h4 class="center" style="padding: 15px;">No Size Group Found</h4>
        </div>
    @endif
    <button type="button" id="sizeGroupModalDone" class="btn btn-primary btn-sm">Done</button>
</div>