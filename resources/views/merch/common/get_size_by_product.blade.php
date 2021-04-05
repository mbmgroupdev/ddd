<style type="text/css">
    span.sizes{
        background: rgb(8 155 171);
        color: #fff;
        padding: 3px 5px 3px 5px;
        z-index: 0;
        margin-bottom: 3px;
        margin-left: 30px;
        display: inline-block;
    }
    .ace-checkbox{
        zoom: 1.5;
        position: absolute;
        top: 3px;
        left: 5px;
    }
    ul.size-group  {
      column-count: 8;
      column-gap: 40px;
      column-rule-style: solid;
      column-rule-width: 1px;
      column-rule-color: lightblue;
    }
</style>
<div class="col-sm-12"><div class="checkbox">
    @if(count($sizeList) > 0) 
        @foreach ($sizeList as $key => $v)
            <label class="col-sm-12" style="padding:0px;">
                <input name='sizeGroups[]' type='radio' id='sizeGroups-{{$key}}' class='ace-checkbox' value='{{$key}}'>
                <span class='lbl sizes'> {!!$sizegroupList[$key]!!}</span>
            @if(count($v) > 0)
            <ul class="size-group">
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