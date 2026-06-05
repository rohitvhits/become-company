-- SQL to add Announcement Master permissions to the database
-- Run this SQL in your database after running migrations

INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('announcement-master-list', 'web', NOW(), NOW()),
('announcement-master-create', 'web', NOW(), NOW()),
('announcement-master-edit', 'web', NOW(), NOW()),
('announcement-master-delete', 'web', NOW(), NOW());

-- After inserting permissions, assign them to the appropriate roles
-- For example, to assign all permissions to Admin role (assuming role_id = 1):

-- INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
-- SELECT id, 1 FROM `permissions` WHERE name LIKE 'announcement-master-%';

-- Note: Adjust the role_id as per your database structure
