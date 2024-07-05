SELECT organization_family_id, 
first_name, last_name, role FROM organization_members GROUP BY organization_family_id, first_name, last_name, role HAVING COUNT(*) > 1;


DELETE FROM organization_members
WHERE id IN (
    SELECT id
    FROM (
        SELECT id,
               ROW_NUMBER() OVER (PARTITION BY organization_family_id, first_name, last_name, role ORDER BY id) AS row_num
        FROM organization_members
    ) temp
    WHERE row_num > 1
);


DELETE FROM organization_members
WHERE id IN (
    SELECT id
    FROM (
        SELECT id,
               @row_num := IF(@prev_family = organization_family_id AND @prev_first = first_name AND @prev_last = last_name AND @prev_role = role, @row_num + 1, 1) AS row_num,
               @prev_family := organization_family_id,
               @prev_first := first_name,
               @prev_last := last_name,
               @prev_role := role
        FROM organization_members
        CROSS JOIN (SELECT @row_num := 0, @prev_family := NULL, @prev_first := NULL, @prev_last := NULL, @prev_role := NULL) AS vars
        ORDER BY organization_family_id, first_name, last_name, role, id
    ) temp
    WHERE row_num > 1
);

-- delete families with no student
DELETE om
FROM organization_members om
JOIN (
    SELECT id
    FROM organization_members
    WHERE organization_family_id IN (
        SELECT organization_family_id
        FROM organization_members
        GROUP BY organization_family_id
        HAVING SUM(role = 2) = 0
    )
    LIMIT 20
) AS to_delete ON om.id = to_delete.id;