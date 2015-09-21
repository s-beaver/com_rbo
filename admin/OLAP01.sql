SELECT *,
   CASE
   WHEN CAST(t0.sMonth AS SIGNED)-CAST(t0.Period AS SIGNED)>=0 THEN t0.sYear
   ELSE CAST(t0.sYear AS SIGNED)-1
   END PeriodYear
  FROM (
SELECT operId,oper_date,
  oper_sum,oper_rem,
  CASE 
    WHEN LOWER(oper_rem) like '%����%' THEN '�������'
    WHEN LOWER(oper_rem) like '%�������%' THEN '�������'
    WHEN LOWER(oper_rem) like '%�������%' THEN '�������'
    WHEN LOWER(oper_rem) like '%�������%' THEN '�������'
    WHEN LOWER(oper_rem) like '%����%' THEN '��������'
    WHEN LOWER(oper_rem) like '%��������%' THEN '������'
    WHEN LOWER(oper_rem) like '%���%' THEN '���'
    WHEN LOWER(oper_rem) like '%����%' THEN '����'
    ELSE 'n/a'
  END AS 'Empl',
  CASE 
    WHEN LOWER(oper_rem) like '%������%' THEN '01'
    WHEN LOWER(oper_rem) like '%�������%' THEN '02'
    WHEN LOWER(oper_rem) like '%����%' THEN '03'
    WHEN LOWER(oper_rem) like '%������%' THEN '04'
    WHEN LOWER(oper_rem) like '%���%' THEN '05'
    WHEN LOWER(oper_rem) like '%����%' THEN '06'
    WHEN LOWER(oper_rem) like '%����%' THEN '07'
    WHEN LOWER(oper_rem) like '%������%' THEN '08'
    WHEN LOWER(oper_rem) like '%��������%' THEN '09'
    WHEN LOWER(oper_rem) like '%�������%' THEN '10'
    WHEN LOWER(oper_rem) like '%������%' THEN '11'
    WHEN LOWER(oper_rem) like '%�������%' THEN '12'
    ELSE 'n/a'
  END AS Period,
 	DATE_FORMAT(oper_date,'%y') sYear, 
	DATE_FORMAT(oper_date,'%m') sMonth, 
	DATE_FORMAT(oper_date,'%d') sDay
FROM rbo_opers 
WHERE oper_type='��������' AND DATE_FORMAT(oper_date,'%y')>=14
) t0
ORDER BY oper_date