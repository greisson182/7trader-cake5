-- Índices que melhoram performance das queries mais comuns

CREATE INDEX IF NOT EXISTS `idx_students_user_id` ON `students` (`user_id`);

CREATE INDEX IF NOT EXISTS `idx_studies_student_date` ON `studies` (`student_id`, `study_date`);
CREATE INDEX IF NOT EXISTS `idx_studies_student_year` ON `studies` (`student_id`, `study_date`);
CREATE INDEX IF NOT EXISTS `idx_studies_profit_loss` ON `studies` (`profit_loss`);

CREATE INDEX IF NOT EXISTS `idx_operations_student` ON `operations` (`student_id`);
CREATE INDEX IF NOT EXISTS `idx_operations_account` ON `operations` (`account_id`);
CREATE INDEX IF NOT EXISTS `idx_operations_date` ON `operations` (`data_operation`);
CREATE INDEX IF NOT EXISTS `idx_operations_student_date` ON `operations` (`student_id`, `data_operation`);

CREATE INDEX IF NOT EXISTS `idx_operations_costs_operation` ON `operations_costs` (`operation_id`);

CREATE INDEX IF NOT EXISTS `idx_weekly_controls_student` ON `weekly_controls` (`student_id`);
CREATE INDEX IF NOT EXISTS `idx_weekly_controls_week` ON `weekly_controls` (`week_id`);
CREATE INDEX IF NOT EXISTS `idx_weekly_controls_student_week` ON `weekly_controls` (`student_id`, `week_id`);

CREATE INDEX IF NOT EXISTS `idx_course_videos_course_order` ON `course_videos` (`course_id`, `order_position`);
CREATE INDEX IF NOT EXISTS `idx_student_progress_student_video` ON `student_progress` (`student_id`, `video_id`);
CREATE INDEX IF NOT EXISTS `idx_course_enrollments_student_course` ON `course_enrollments` (`student_id`, `course_id`);

CREATE INDEX IF NOT EXISTS `idx_files_user` ON `files` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_files_status` ON `files` (`status`);
