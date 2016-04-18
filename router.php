<?php
defined('_JEXEC') or die;

function RbOBuildRoute(&$query)
{
    $segments = array();
    /*if(isset($query['option']))
    {
        $segments[] = $query['option'];
        unset($query['option']);
    }*/
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
    if (isset($query['format'])) {
        $segments[] = $query['format'];
        unset($query['format']);
    }
    if (isset($query['docid'])) {
        $segments[] = $query['docid'];
        unset($query['docid']);
    }
    return $segments;
}

function RbOParseRoute($segments)
{
    $vars = array();
    $vars['view'] = $segments[0];
    $i=1;
    if ($segments[$i] == 'raw')
        $vars['format'] = $segments[$i];
    $i++;
    $vars['docid'] = $segments[$i];
    return $vars;
}

/*
http://robik.ru/zakrytye-materialy/rbo
/index.php?option=com_rbo&view=invoices -> invoices
/index.php?option=com_rbo&view=shipments -> shipments
/index.php?option=com_rbo&view=acts
/index.php?option=com_rbo&view=docbbill
/index.php?option=com_rbo&view=docbact
/index.php?option=com_rbo&view=products
/index.php?option=com_rbo&view=customers

/index.php?option=com_rbo&view=PrnInStock&format=raw
/index.php?option=com_rbo&view=PrnProdVed&format=raw
/index.php?option=com_rbo&view=PrnInv&format=raw&docid=1079
*/