
<link href="{{ asset('assets/fullcalendar/core/main.css')}}" rel='stylesheet' />
<link href="{{ asset('assets/fullcalendar/daygrid/main.css')}}" rel='stylesheet' />
<link href="{{ asset('assets/fullcalendar/timegrid/main.css')}}" rel='stylesheet' />
<link href="{{ asset('assets/fullcalendar/list/main.css')}}" rel='stylesheet' />
<script src="{{ asset('assets/js/moment.min.js') }}"></script>  
<script src="{{ asset('assets/fullcalendar/core/main.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/daygrid/main.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/timegrid/main.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/list/main.js') }}"></script> 
{!! $calendar->calendar() !!}
{!! $calendar->script() !!} 

