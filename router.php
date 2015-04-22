<?php
defined('_JEXEC') or die;

function RbOBuildRoute (&$query)
{
	$segments = array(); 
	$segments[] = $query['task'];
	$segments[] = $query['view'];
	$segments[] = $query['inv_num'];
	unset($query['task']);
	unset($query['view']);
	unset($query['inv_num']);
	return $segments;
}

function RbOParseRoute($segments)
{
	$vars = array();
	$vars['task'] = $segments[0];
	$vars['view'] = $segments[1];
	$vars['inv_num'] = $segments[2];
	return $vars;
}
