<?php
defined('_JEXEC') or die;

function RbOBuildRoute (&$query)
{
	$segments = array(); 
	$segments[] = $query['view'];
	unset($query['view']);
	return $segments;
}

function RbOParseRoute($segments)
{
	$vars = array();
	$vars['view'] = $segments[1];
	return $vars;
}
