SELECT f.feature AS "Feature", COUNT(f.feature) AS "# Used"
FROM FeatureUse as f
	LEFT JOIN Account as a on a.id = f.usedBy
WHERE 
	f.usedOn > "2015-01-01" 
    AND
    f.usedOn < "2015-07-01" 
    AND
    NOT (f.feature = 'Calendar.iCal')
GROUP BY f.feature