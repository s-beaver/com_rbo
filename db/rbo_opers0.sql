-- подготовка к замене cust_name на custId
UPDATE rbo_opers ro SET ro.oper_rem = CONCAT(IFNULL(ro.oper_rem,"")," ",ro.cust_name) WHERE LENGTH(ro.cust_name)>0