<?php
class Db_optimize extends crm_controller {
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
        $this->load->library('validation');
		$this->load->library('email');
    }
    
    function index()
	{	
		echo "<br> Start Date =".date("Y-m-d H:i:s");
		$tbl_array = array('crm_additional_cats',
'crm_additional_items',
'crm_book_keeping_currency_rates',
'crm_client_logo',
'crm_commission_history',
'crm_commission_history_log',
'crm_commission_uploads',
'crm_commission_uploads_mapping',
'crm_contracts',
'crm_contracts_logs',
'crm_contracts_uploads',
'crm_contracts_uploads_mapping',
'crm_contract_jobs',
'crm_cost_center',
'crm_country',
'crm_crons',
'crm_crons_notificatons',
'crm_currency_all',
'crm_currency_rate',
'crm_customers',
'crm_customers_company',
'crm_default_folder',
'crm_default_folders',
'crm_department',
'crm_deposits',
'crm_dms_files',
'crm_dms_file_management',
'crm_dms_folder_access',
'crm_dms_users',
'crm_dns',
'crm_email_template',
'crm_email_template_hf',
'crm_expected_payments',
'crm_expected_payments_attach_file',
'crm_expect_worth',
'crm_file_management',
'crm_hosting',
'crm_hosting_package',
'crm_industry',
'crm_items',
'crm_job_urls',
'crm_leads',
'crm_lead_files',
'crm_lead_file_access',
'crm_lead_folder_access',
'crm_lead_query',
'crm_lead_services',
'crm_lead_source',
'crm_lead_stage',
'crm_lead_stage_history',
'crm_lead_status_history',
'crm_levels',
'crm_levels_country',
'crm_levels_location',
'crm_levels_region',
'crm_levels_state',
'crm_location',
'crm_logs',
'crm_masters',
'crm_master_roles',
'crm_milestones',
'crm_package',
'crm_package_type',
'crm_practices',
'crm_practice_max_hours_history',
'crm_profit_center',
'crm_project_billing_type',
'crm_project_dashboard_fields',
'crm_project_folder_access',
'crm_project_other_cost',
'crm_project_plan',
'crm_project_types',
'crm_region',
'crm_roles',
'crm_sales_divisions',
'crm_sales_forecast',
'crm_sales_forecast_milestone',
'crm_sales_forecast_milestone_audit_log',
'crm_saved_search_critriea',
'crm_services_dashboard',
'crm_services_dashboard_beta',
'crm_services_graphical_dashboard',
'crm_sessions',
'crm_stake_holders',
'crm_state',
'crm_subscriptions_type',
'crm_taskremarks',
'crm_tasks',
'crm_tasks_qc',
'crm_tasks_track',
'crm_task_alert',
'crm_task_category',
'crm_timesheet_data',
'crm_users',
'crm_user_attendance',
'crm_user_roles',
'crm_view_econnect_mas',
'crm_view_sales_forecast_variance');

		foreach($tbl_array as $tbl){
			$this->db->query("OPTIMIZE TABLE ".$tbl);
			echo $this->db->last_query() . "<br>";
		}
		echo "<br>Started = ".date("Y-m-d H:i:s");
		echo "<br>Table Optimized";
	}	
}
?>