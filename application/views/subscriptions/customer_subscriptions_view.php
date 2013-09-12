<?php $this->load->view('tpl/header'); ?>

<div id="content">
    <div id="left-menu">
		<a href="customers/">Back To Customer List</a>
		<a href="customers/add_customer/update/<?php echo $id; ?>">Back To Customer</a>
	</div>
    <div class="inner">
		<div id="recurring-items">
			<div class="management-box-title">
				<h3>Subscriptions</h3>
				<a href="javascript:void(0)" class="add-item" title="Add Recurring Item"><img src="assets/img/icon_add.png"></a>
			</div>
			<div class="item-total" style="overflow: hidden;">
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" charset="utf-8">
	$(function() {
		customer_id = <?php echo $id; ?>;
		
		function get_subscription_costs() {
			$.get('subscriptions/get_subscription_costs/' + customer_id, function(data) {
				$("#recurring-items .item-total").html(data);
			});
		}
		
		if (customer_id > 0) {
			$("#recurring-items").show();
			get_subscription_costs();
			$.get('subscriptions/get_recurring_items/' + customer_id, function(data) {
				$("#recurring-items .management-box-title").after(data);
			});
		}

		$('#recurring_item_desc').livequery(function(){
			$(this).keyup(function(){
				var desc_len = $(this).val();

				if (desc_len.length > 600) {
					$(this).val(desc_len.substring(0, 600));
				}

				var remain_len = 600 - desc_len.length;
				if (remain_len < 0) remain_len = 0;

				$('#desc-countdown-recurring').text(remain_len);
			});
		});

		$("#recurring-items .management-box-title a.add-item").click(function() {
			if ($("#recurring-items .add-recurring-item").length == 0) {
				$.get('subscriptions/new_item_form/' + customer_id, function(data) {
					$("#recurring-items .management-box-title").after(data);
					$("#recurring-items .add-recurring-item").parent().slideDown();
				});
			}
		});

		$("#recurring-items .recurring-item .edit").livequery(function() {
			$(this).click(function () {
				$that = $(this);
				$.get('subscriptions/edit_recurring_item/' + $that.attr('rel'), function(data) {
					$("#recurring-items .add-recurring-item").parent().slideUp(function() {
						$that = $(this);
						if ($(this).parents('.recurring-item').length > 0) {
							$.get('subscriptions/get_recurring_item/' + $that.parents('.recurring-item').attr('rel'), function(data) {
								$that.parents('.recurring-item').replaceWith(data);
							});
						} else {
							$(this).remove();
						}
					});
					$that.parents('.recurring-item').html(data).find('.add-recurring-item').parent().slideDown();
				});
			});
		});

		$("#recurring-items .recurring-item .discount").livequery(function() {
			$(this).click(function () {
				if ($("#recurring-items .add-recurring-item").length == 0) {
					$that = $(this);
					$.get('subscriptions/new_discount_form/' + customer_id + '/' + $that.attr('rel'), function(data) {
						$("#recurring-items .add-recurring-item").parent().remove();
						$that.parents('.recurring-item').after(data);
						$("#recurring-items .add-recurring-item").parent().slideDown().css('border-color', '#529214');
					});
				}
			});
		});

		$("#recurring-items .recurring-item .delete").livequery(function() {
			$(this).click(function () {
				if (confirm("Are you sure?")) {
					$that = $(this);
					$.get('subscriptions/delete_recurring_item/' + $that.attr('rel'), function(data) {
						$("#recurring-items .recurring-item[rel='" + $that.attr('rel') + "'], #recurring-items .parent-id-" + $that.attr('rel')).slideUp(function() {
							$(this).remove();
							get_subscription_costs();
						});
					});
				}
			});
		});

		$('#recurring-items #submit-recurring-item').livequery(function(){
			$(this).click(function(){
				if ($("#recurring-items .add-recurring-item #recurring_item_desc").val() == '') {
					alert('Item Description is Required');
				} else {
					if ($("#recurring_hidden_recurringitemid").val() != '') {
						$.post('subscriptions/update_recurring_item/' + $("#recurring_hidden_recurringitemid").val(), $("#recurring-items .add-recurring-item").serialize(), function(data) {
							$("#recurring-items .add-recurring-item").parent().slideUp(function() {
								$("#recurring-items .recurring-item[rel='" + $("#recurring_hidden_recurringitemid").val() + "']").replaceWith(data);
								get_subscription_costs();
							});
						});
					} else {
						$.post('subscriptions/new_recurring_item/' + customer_id, $("#recurring-items .add-recurring-item").serialize(), function(data) {
							$("#recurring-items .add-recurring-item").parent().slideUp(function() {
								if ($("#recurring_hidden_parent_id").val() != '') {
									$("#recurring-items .recurring-item[rel='" + $("#recurring_hidden_parent_id").val() + "']").after(data);
								} else {
									$("#recurring-items .management-box-title").after(data);
								}
								$(this).remove();
								get_subscription_costs();
							});
						});
					}
				}

				return false;
			});
		});

		$('#recurring-items #add-to-recurring-library').livequery(function(){
			$(this).click(function(){

				return false;
			});
		});

		$('#recurring-items #open-prefill-window').livequery(function(){
			$(this).click(function(){

				return false;
			});
		});

		$('#recurring-items #cancel-recurring-item').livequery(function(){
			$(this).click(function(){

				$("#recurring-items .add-recurring-item").parent().slideUp(function() {
					$that = $(this);
					if ($(this).parents('.recurring-item').length > 0) {
						$.get('subscriptions/get_recurring_item/' + $that.parents('.recurring-item').attr('rel'), function(data) {
							$that.parents('.recurring-item').replaceWith(data);
							$("#recurring-items .add-recurring-item").remove();
						});
					} else {
						$(this).remove();
					}
				});

				return false;
			});
		});
	});
</script>

<?php $this->load->view('tpl/footer'); ?>