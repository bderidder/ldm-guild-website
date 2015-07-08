SELECT a.displayName AS "Account", COUNT(a.displayName) AS "# Features Used"
FROM FeatureUse as f
	LEFT JOIN Account as a on a.id = f.usedBy
WHERE 
	f.usedOn > "2015-05-01" 
    AND
    f.usedOn < "2015-06-01" 
    AND
    NOT (f.feature = 'Calendar.iCal')
GROUP BY a.displayName