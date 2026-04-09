<?php

return [
    // V2 Copy Contracts — Empty states
    'empty_map' => 'Aún no hay reportes en esta zona. Sé el primero en ayudar a tu comunidad.',

    // V2 Copy Contracts — Rate limiting
    'rate_limit_building' => 'Ya enviaste un reporte para este edificio hoy. Gracias por seguir participando.',
    'rate_limit_global' => 'El sistema está muy ocupado en este momento. Tu reporte está guardado y se enviará pronto.',

    // V2 Copy Contracts — Photo
    'photo_too_large' => 'Esta foto es demasiado grande para enviar. Intenta tomar una nueva — no necesitas alta calidad, solo que se vea el daño con claridad.',
    'photo_guidance_cta_first' => 'Permitir acceso a la cámara',
    'photo_guidance_cta_repeat' => 'Abrir cámara',

    // V2 Copy Contracts — AI classification turn-taking
    'ai_analyzing' => 'Analizando tu foto...',
    'ai_suggestion_prompt' => 'Creemos que el nivel de daño es :level. ¿Te parece correcto?',
    'ai_not_ready' => 'El análisis aún no está listo — por favor elige el nivel de daño.',

    // V2 Copy Contracts — Confirmation (online vs offline)
    'confirmation_online' => 'Tu reporte fue recibido.',
    'confirmation_offline' => 'Tu reporte está guardado. Se enviará automáticamente cuando tengas conexión. No necesitas hacer nada más.',
    'confirmation_report_id' => 'Número de reporte: #:id',
    'confirmation_thanks' => 'Gracias por ayudar a tu comunidad.',
    'confirmation_joins' => 'Tu reporte se une a otros :count de :area.',

    // V2 Copy Contracts — Account creation CTA (Hall Lesson 6)
    'account_cta' => 'Recibe notificaciones cuando los equipos de campo lleguen a tu zona',

    // V2 Copy Contracts — Conflict mode banner
    'conflict_mode_banner' => 'Modo anónimo. No almacenamos nada que pueda identificarte.',

    // V2 Transparency Screen — Standard mode
    'transparency_standard_1' => 'Tu foto y ubicación se envían a los equipos de emergencia del PNUD',
    'transparency_standard_2' => 'Tu reporte aparece en un mapa de daños en tiempo real',
    'transparency_standard_3' => 'Nunca se recopila tu nombre, teléfono ni correo electrónico',
    'transparency_standard_4' => 'Puedes detenerte en cualquier momento',
    'transparency_standard_cta' => 'Comenzar reporte',
    'transparency_standard_learn_more' => 'Cómo se protegen tus datos',

    // V2 Transparency Screen — Conflict mode
    'transparency_conflict_1' => 'No se recopila tu nombre, teléfono ni correo electrónico',
    'transparency_conflict_2' => 'Después de enviar, no se guarda nada en tu dispositivo',
    'transparency_conflict_3' => 'Puedes cerrar esta aplicación en cualquier momento',
    'transparency_conflict_4' => 'Tu reporte ayuda al PNUD a entender lo que está pasando en tu zona',
    'transparency_conflict_cta' => 'Enviar un reporte',
    'transparency_conflict_learn_more' => 'Cómo funciona esto',

    // V2 Recovery Outcome banner
    'recovery_update_from' => 'Una actualización de :area:',
    'recovery_contributed' => 'Tus reportes contribuyeron a este resultado.',

    // V2 Trusted device
    'trusted_contributor' => 'Colaborador de confianza',

    // UI — Confirmation screen
    'report_submitted' => 'Reporte enviado',
    'report_submitted_desc' => 'Tu reporte de daños se registró correctamente.',
    'report_id_label' => 'Número de reporte:',
    'submitted_label' => 'Enviado:',

    // UI — Engagement panel
    'community_contributions' => 'Aportes de la comunidad',
    'community_contributions_desc' => 'Cada reporte ayuda a que los equipos de respuesta lleguen más rápido a quienes lo necesitan.',
    'community_count_label' => 'Miembros de la comunidad han enviado reportes',
    'user_count_label' => 'Reportes que has enviado',
    'achievements' => 'Logros',
    'earned' => 'Obtenido',
    'locked' => 'Bloqueado',

    // UI — Navigation
    'progress_ring_title' => 'Cobertura de zona',
    'progress_ring_desc' => ':percent% de edificios en tu zona han sido reportados',
    'progress_ring_buildings' => ':reported de :total edificios',
    'leaderboard_title' => 'Mejores contribuyentes',
    'leaderboard_reports' => ':count reportes',
    'leaderboard_you' => 'Tu',
    'leaderboard_anonymous' => 'Los reportes anonimos no se clasifican',

    'app_name' => 'RAPIDA',
    'status_online' => 'En línea',
    'status_offline' => 'Sin conexión',
    'status_syncing' => ':count sincronizando',
    'safe_exit' => 'Salida segura',
    'safe_exit_aria' => 'Salida segura — salir rápidamente de esta página',

    // UI — Report detail
    'report_id' => 'Número de reporte',
    'infrastructure' => 'Infraestructura',
    'crisis_type' => 'Tipo de crisis',
    'reported_by' => 'Reportado por',
    'community_reporter' => 'Miembro de la comunidad',
    'submitted' => 'Enviado',
    'location' => 'Ubicación',
    'version_history' => 'Historial de versiones',
    'current_version' => 'Versión actual',
    'back_to_reports' => 'Volver a mis reportes',

    // UI — My Reports
    'my_reports' => 'Mis reportes',
    'my_reports_desc' => 'Tus reportes de daños enviados. Tus datos siempre te pertenecen.',
    'synced' => 'Sincronizado',
    'pending' => 'Pendiente',
    'no_reports_yet' => 'Aún no has enviado ningún reporte.',
    'submit_new_report' => 'Enviar nuevo reporte',

    // UI — Map home
    'dashboard_field' => 'Mapa de campo',
    'dashboard_analyst' => 'Analista',
    'dashboard_export' => 'Exportar',

    'report_damage' => 'Reportar daños',
    'recent_reports' => 'Reportes recientes',
    'no_reports_community' => 'Aún no se han enviado reportes.',
    'be_first' => 'Sé el primero en ayudar a tu comunidad.',

    // UI — Damage levels
    'damage_level_label' => 'Nivel de daño',
    'damage_minimal' => 'Mínimo',
    'damage_partial' => 'Parcial',
    'damage_complete' => 'Total',

    // UI — Sync status
    'sync_synced' => 'Sincronizado',
    'sync_pending' => 'Pendiente de sincronización',
    'sync_failed' => 'Error de sincronización',
];
