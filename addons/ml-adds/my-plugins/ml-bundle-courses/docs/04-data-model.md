# Модель данных (v0.2.0)

## 1) Программы (CPT)
- `post_type`: `ml_program`

### Метаполя программы
- `mlp_steps` — массив шагов, структура:
  - `term_id` (int)
  - `duration` (int)
  - `units` ("days" | "months")

## 2) Привязка пользователя (user_meta)
Состояние пользователя хранится в `wp_usermeta`:
- `mlp_program_id` — ID записи `ml_program`
- `mlp_current_step` — индекс текущего шага
- `mlp_last_certificate_hash` — защита от повторной обработки (идемпотентность)
