-- This query get all members of a family who have a user regirsted against their email but is not linked with the user_id. 
SELECT *
FROM organization_members m
WHERE m.user_id IS NULL
  AND EXISTS (SELECT 1 FROM users u WHERE u.email = m.email);  

-- Following query gets same but for specific school. In this case school with ID 8
SELECT m.*
FROM organization_members m
JOIN organization_families of ON m.organization_family_id = of.organization_family_id
WHERE m.user_id IS NULL
  AND of.org_id = 8
  AND EXISTS (SELECT 1 FROM users u WHERE u.email = m.email);

  -- Update all memeber with no id to correct id if user exist matching email

  UPDATE organization_members m
JOIN users u ON m.email = u.email
SET m.user_id = u.id
WHERE m.user_id IS NULL;

-- check how many rows above query will update
SELECT COUNT(*)
FROM organization_members m
JOIN users u ON m.email = u.email
WHERE m.user_id IS NULL;

-- Update with a limit
UPDATE organization_members m
JOIN (
    SELECT m.id AS org_member_id, u.id AS user_user_id
    FROM organization_members m
    JOIN users u ON m.email = u.email
    WHERE m.user_id IS NULL
    LIMIT 1
) AS subquery ON m.id = subquery.org_member_id
SET m.user_id = subquery.user_user_id;