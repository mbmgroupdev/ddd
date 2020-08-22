
@if(Session::has('success'))
<script type="text/javascript">
  $.growl.notice({ message: "{!! Session::get('success')!!}" });
</script>
@endif

@if(Session::has('warning'))
<script type="text/javascript">
  $.growl.warning({ message: "{!! Session::get('warning')!!}" });
</script>
@endif

@if(Session::has('error'))
<script type="text/javascript">
  $.growl.error({ message: "{!! Session::get('error')!!}" });
</script>
@endif
