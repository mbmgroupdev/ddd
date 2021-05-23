<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    font-size: 12px !important;
				    display: inline-block;
				}

				.page-break{
                    page-break-after: always;
                }
                .page-break p{
                    
                    line-height: 16px;
                }
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:54pt 36pt }
		</style>
		@foreach($employees as $key => $emp)
		<div id="jc-{{$emp->associate_id}}" class="bn-form-output page-break" >
			<h2 class="text-center">ফরম-৩৫</h2>
			<h2 class="text-center">[ধারা ১০৯ এবং বিধি ১০৩ (১) দ্রষ্টব্য]</h2>
			<p class="text-center">মহিলাদের রাত্রিকালীন কাজ করিবার সম্মতি পত্র।</p>
			<br><br><br>
			<p class="mb-2">
				<span>কারখানা/প্রতিষ্ঠানের নাম</span>:  
				@if(isset($unit[$emp->as_unit_id]))
					{{$unit[$emp->as_unit_id]['hr_unit_name_bn']}}
				@endif
			</p>
			<p class="mb-2">
				<span>কারখানা/প্রতিষ্ঠানের ঠিকানা</span>:
				@if(isset($unit[$emp->as_unit_id]))
					{{$unit[$emp->as_unit_id]['hr_unit_address_bn']}}
				@endif
			</p>
			<p class="mb-2"><span>শ্রমিকের নাম</span>: {{$emp->hr_bn_associate_name}}</p>
			<p class="mb-2">
				<span>পদবী</span>: 
				@if(isset($designation[$emp->as_designation_id]))
					{{$designation[$emp->as_designation_id]['hr_designation_name_bn']}}
				@endif
			</p>
			<p class="mb-2"> <span>আইডি</span>: {{$emp->associate_id}}</p>
			@if($emp->as_oracle_code != null)
				<p class="mb-2"><span>পূর্বের আইডি</span>: {{$emp->as_oracle_code}}</p>
			@endif
			<p class="mb-2">
				<span>শাখা</span>:
				@if(isset($section[$emp->as_section_id]))
					{{$section[$emp->as_section_id]['hr_section_name_bn']}}
				@endif 
				@if(isset($department[$emp->as_department_id]))
					, {{$department[$emp->as_department_id]['hr_department_name_bn']}}
				@endif

			</p>

			<br>
			<br>
			<p class="text-justify">আমি এতদ্বারা ঘোষনা করিতেছি যে, ব্যবস্থাপনা কর্তৃপক্ষ কর্তৃক কাজের সময় যথাযথ নিরাপত্তা নিশ্চিত করিবার শর্তে উক্ত প্রতিষ্ঠানের নৈশ পালায় রাত্রি ১০ (দশ) ঘটিকা হইতে ভোর ০৬ (ছয়) ঘটিকা পর্যন্ত কাজ করিতে আমি সম্মত রহিয়াছি।</p>
			<br>
			<p class="text-justify">উক্ত সম্মতি পত্র আমা কর্তৃক বাতিল না করা হইলে উহা আগামী ০১ (এক) বছর পর্যন্ত কার্যকর থাকিবে।</p>
			<br>
			<br>
			<br>
			<br>
			<p>স্বাক্ষর/টিপসহি</p>
			<br>
			<br>
			<p>তারিখঃ........................... </p>
		</div>
		 
		@endforeach
	</div>
</div>                                                                                                            