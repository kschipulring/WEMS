<?php
class config{
	const CLEANABLE_TARGET_TABLE = "WEMS_CLEANABLE_TARGET";
	const CLEANABLE_TARGET_NOTES_TABLE = "WEMS_CLEANABLE_TARGET_NOTES";
	const DEPT_TABLE = "DEPT";
	const WEMS_DEPT_TABLE = "WEMS_DEPT";
	const EMPLOYEE_TABLE = "EMPLOYEE";
	const WEMS_EMPLOYEE_TABLE = "WEMS_EMPLOYEE";
	const GANG_TABLE = "WEMS_GANG";
	const GANG_NOTES_TABLE = "WEMS_GANG_NOTES";
	const LOCATION_TABLE = "WEMS_LOCATION";
	const LOCDOCS_TABLE = "WEMS_LOCDOCS";
	const LOCATION_STATUS_TABLE = "WEMS_LOCATION_STATUS";
	
	/*
	 * for logging in, user must be a member of one of the following LDAP groups
	 * the lowest the number a value is in the array, the fewer priviliges it has.
	 * e.g., WEMS_Read = 0, having the lowest possible amount of access, WEMS_Admin = 2, currently the highest level access
	 */
	public static $approvedGroups = array(
		"WEMS_Read",
		"WEMS_Write",
		"WEMS_Admin"
	);
}