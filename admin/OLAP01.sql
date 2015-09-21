SELECT *,
   CASE
   WHEN CAST(t0.sMonth AS SIGNED)-CAST(t0.Period AS SIGNED)>=0 THEN t0.sYear
   ELSE CAST(t0.sYear AS SIGNED)-1
   END PeriodYear
  FROM (
SELECT operId,oper_date,
  oper_sum,oper_rem,
  CASE 
    WHEN LOWER(oper_rem) like '%себе%' THEN 'Николай'
    WHEN LOWER(oper_rem) like '%николаю%' THEN 'Николай'
    WHEN LOWER(oper_rem) like '%алексею%' THEN 'Алексей'
    WHEN LOWER(oper_rem) like '%вяткину%' THEN 'Алексей'
    WHEN LOWER(oper_rem) like '%вове%' THEN 'Владимир'
    WHEN LOWER(oper_rem) like '%николаич%' THEN 'Андрей'
    WHEN LOWER(oper_rem) like '%ввп%' THEN 'ВВП'
    WHEN LOWER(oper_rem) like '%Анне%' THEN 'Анна'
    ELSE 'n/a'
  END AS 'Empl',
  CASE 
    WHEN LOWER(oper_rem) like '%январь%' THEN '01'
    WHEN LOWER(oper_rem) like '%февраль%' THEN '02'
    WHEN LOWER(oper_rem) like '%март%' THEN '03'
    WHEN LOWER(oper_rem) like '%апрель%' THEN '04'
    WHEN LOWER(oper_rem) like '%май%' THEN '05'
    WHEN LOWER(oper_rem) like '%июнь%' THEN '06'
    WHEN LOWER(oper_rem) like '%июль%' THEN '07'
    WHEN LOWER(oper_rem) like '%август%' THEN '08'
    WHEN LOWER(oper_rem) like '%сентябрь%' THEN '09'
    WHEN LOWER(oper_rem) like '%октябрь%' THEN '10'
    WHEN LOWER(oper_rem) like '%ноябрь%' THEN '11'
    WHEN LOWER(oper_rem) like '%декабрь%' THEN '12'
    ELSE 'n/a'
  END AS Period,
 	DATE_FORMAT(oper_date,'%y') sYear, 
	DATE_FORMAT(oper_date,'%m') sMonth, 
	DATE_FORMAT(oper_date,'%d') sDay
FROM rbo_opers 
WHERE oper_type='зарплата' AND DATE_FORMAT(oper_date,'%y')>=14
) t0
ORDER BY oper_date