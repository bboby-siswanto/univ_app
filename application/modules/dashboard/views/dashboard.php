<div class="container-fluid">
    <div class="animated fadeIn">
	    
	    <div class="card">
		    <div class="card-header">
			    <a href="#collapsable_container" data-toggle="collapse">Filter</a>
			    <div class="float-right">
				    <a href="#collapsable_container" data-toggle="collapse"><i class="fas fa-arrow-down"></i></a>
			    </div>
		    </div>
		    
		    <div class="collapse" id="collapsable_container">
		    
		    <div class="card-body">
			    <form id="filter_form" name="filter_form" action="<?=site_url('dashboard/get_dashboard_data')?>" method="POST">
				    <div class="row">
					    <div class="col-md-12">
						    <div class="form-group">
							    <label for="Start Date">Date range</label>
							    <input type="text" id="date_range" name="date_range" class="form-control">
						    </div>
					    </div>
				    </div>
			    </form>
		    </div>
		    <div class="card-footer">
			    <button id="btn_filter" type="button" class="btn btn-md btn-info">Filter</button>
		    </div>
		    
		    </div>
	    </div>
	    
        <div class="row">
	        
            <div class="col-sm-6 col-lg-6">
                <div class="brand-card">
                    <div class="card-header bg-primary text-center">
                        <h1 class="display-3" id="disp_candidates">-</h1>
                        <strong>Total Candidates<div class="year">-</div></strong>
                    </div>
                    <div class="card-body">
	                    <div class="my-1">
		                    <button class="btn btn-block btn-info" data-toggle="collapse" data-target="#accordion_candidate_comparison"><i class="fas fa-chart-line"></i> Year to year</button>
	                    	<div class="collapse multi-collapse" id="accordion_candidate_comparison">
		                    	<div class="card card-body">
			                    	<div class="row">
				                    	<div class="col-md-6">
					                    	<h4>Previous year</h4>
					                    	<ul class="list-group" id="previous_candidate_stats_container">
											</ul>
				                    	</div>
				                    	<div class="col-md-6">
					                    	<h4>Current year</h4>
					                    	<ul class="list-group" id="current_candidate_stats_container">
											</ul>
				                    	</div>
			                    	</div>
			                    </div>
							</div>
	                    </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-6">
                <div class="brand-card">
                    <div class="card-header bg-primary text-center">
                        <h1 class="display-3" id="disp_entrance_test">-</h1>
                        <strong>Entrance Test<div class="year">-</div></strong>
                    </div>
                    <div class="card-body">
	                    <div class="my-1">
		                    <button class="btn btn-block btn-info" data-toggle="collapse" data-target="#accordion_entrance_test">Joined Entrance Test</button>
	                    	<div class="collapse multi-collapse" id="accordion_entrance_test">
		                    	<div class="card card-body">
			                    	<ul class="list-group">
				                    	<li class="list-group-item"><span id="free_et">-</span> Join ET (free)</li>
				                    	<li class="list-group-item"><span id="paid_et">-</span> Join ET (paid)</li>
									</ul>
			                    </div>
							</div>
	                    </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-6">
                <div class="brand-card">
                    <div class="card-header bg-primary text-center">
                        <h1 class="display-3" id="disp_total_paid">-</h1>
                        <strong>Payments<div class="year">-</div></strong>
                    </div>
                    <div class="card-body">
	                    <div class="my-1">
		                    <button class="btn btn-block btn-info" data-toggle="collapse" data-target="#accordion_payment">Highest</button>
	                    	<div class="collapse multi-collapse" id="accordion_payment">
		                    	<div class="card card-body">
			                    	<ul class="list-group">
				                    	<li class="list-group-item"><span id="pending_student">-</span> Pending Students</li>
				                    	<li class="list-group-item"><span id="active_student">-</span> Active Students</li>
									</ul>
			                    </div>
							</div>
	                    </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-6">
                <div class="brand-card">
                    <div class="card-header bg-primary text-center">
                        <h1 class="display-3">100</h1>
                        <strong>Highest Candidate<div class="year">-</div></strong>
                    </div>
                    <div class="card-body">
	                    <div class="my-1">
		                    <button class="btn btn-block btn-info" data-toggle="collapse" data-target="#accordion_highest_candidate">Highest</button>
	                    	<div class="collapse multi-collapse" id="accordion_highest_candidate">
		                    	<div class="card card-body">
			                    	<ul class="list-group" id="highest_candidate_container">
									</ul>
			                    </div>
							</div>
	                    </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
	let filter_form = $('form#filter_form');
	
	$('input#date_range').daterangepicker({
		showDropdowns: true,
		minYear: 2014,
		showWeekNumbers: true,
		autoApply: true,
		minDate: '01/01/2014',
		maxDate: '<?=date('m/d/Y')?>'
	});
	
	function get_dashboard_data(data = false){
		let url = filter_form.attr('action');
		if(data){
			post_data = objectify_form(data);
		}
		else{
			post_data = {
				// date_range: '<?=date('m/d/Y', strtotime(date('m/d/Y')." -1 year"))?> - <?=date('m/d/Y')?>'
				date_range: ''
			}
		}
		
		$.post(url, post_data, function(rtn){
			display_dashboard_data(rtn);
		}, 'json');
	}
	get_dashboard_data();
	
	function display_dashboard_data(data){
		let total_paid_current = (
			data.current_batch.pending + 
			data.current_batch.active + 
			data.current_batch.inactive + 
			data.current_batch.dropout + 
			data.current_batch.resign + 
			data.current_batch.graduated + 
			data.current_batch.onleave
		);
		
		let total_paid_previous = (
			data.previous_batch.pending + 
			data.previous_batch.active + 
			data.previous_batch.inactive + 
			data.previous_batch.dropout + 
			data.previous_batch.resign + 
			data.previous_batch.graduated + 
			data.previous_batch.onleave
		);
		
		$('#disp_candidates').html(data.current_batch.sum);
		$('#disp_entrance_test').html(data.current_batch.participant);
		$('#disp_total_paid').html(data.current_batch.active);
		$('.year').html('Batch ' + data.active_batch);
		$('#current_candidate_stats_container').html(candidate_stats(data.current_batch));
		$('#previous_candidate_stats_container').html(candidate_stats(data.previous_batch));
		$('#free_et').html(data.free_et);
		$('#paid_et').html(data.paid_et);
		$('#pending_student').html(data.current_batch.pending);
		$('#active_student').html(data.current_batch.active);
		$('#highest_candidate_container').html(stats_by_study_program(data.by_study_programs));
	}
	
	function stats_by_study_program(data){
		let container = '';
		
		$.each(data, function(key, value){
			container += '<li class="list-group-item">' + key.toUpperCase() + ': ' + value + '</li>';
		});
		
		return container;
	}
	
	function candidate_stats(data){
		let stats_container = '';
		
		$.each(data, function(key, value){
			if(key != 'sum'){
				stats_container += '<li class="list-group-item">' + key.toUpperCase() + ': ' + value + '</li>';
			}
		});
		
		return stats_container;
	}
	
	$('button#btn_filter').on('click', function(e){
		e.preventDefault();
		let data = filter_form.serializeArray();
		get_dashboard_data(data);
	});
</script>