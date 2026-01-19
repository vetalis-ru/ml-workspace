# Changelog

## v0.2.0
- Реализована пошаговая выдача УД по цепочкам «Сборных курсов»
- Триггер: выдача сертификата (`mbl_certificate_issued`)
- Программы: CPT `ml_program`
- Шаги программы: `post_meta mlp_steps` (term_id + duration + units)
- Привязка пользователя: `user_meta mlp_program_id, mlp_current_step, mlp_last_certificate_hash`
- Админ-интерфейсы: управление программами и участниками
- Расширение: action `mlp_program_step_granted`
