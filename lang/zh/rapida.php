<?php

return [
    // V2 Copy Contracts — Empty states
    'empty_map' => '该区域尚无报告。成为第一个帮助社区的人。',

    // V2 Copy Contracts — Rate limiting
    'rate_limit_building' => '您今天已经为这栋建筑提交过报告了。感谢您的持续参与。',
    'rate_limit_global' => '系统目前非常繁忙。您的报告已保存，稍后会自动发送。',

    // V2 Copy Contracts — Photo
    'photo_too_large' => '这张照片太大，无法发送。请重新拍一张——不需要高画质，能看清损坏情况就好。',
    'photo_guidance_cta_first' => '允许使用相机',
    'photo_guidance_cta_repeat' => '打开相机',

    // V2 Copy Contracts — AI classification turn-taking
    'ai_analyzing' => '正在分析您的照片...',
    'ai_suggestion_prompt' => '我们认为损坏程度为 :level。看起来对吗？',
    'ai_not_ready' => '分析尚未完成——请选择损坏等级。',

    // V2 Copy Contracts — Confirmation (online vs offline)
    'confirmation_online' => '您的报告已收到。',
    'confirmation_offline' => '您的报告已保存。连接网络后会自动发送，您无需做其他操作。',
    'confirmation_report_id' => '报告编号：#:id',
    'confirmation_thanks' => '感谢您帮助社区。',
    'confirmation_joins' => '您的报告与来自 :area 的其他 :count 份报告汇合。',

    // V2 Copy Contracts — Account creation CTA (Hall Lesson 6)
    'account_cta' => '当现场团队到达您所在区域时接收通知',

    // V2 Copy Contracts — Conflict mode banner
    'conflict_mode_banner' => '匿名模式。我们不会存储任何可能识别您身份的信息。',

    // V2 Transparency Screen — Standard mode
    'transparency_standard_1' => '您的照片和位置将发送给联合国开发计划署应急团队',
    'transparency_standard_2' => '您的报告将显示在实时损害地图上',
    'transparency_standard_3' => '我们绝不会收集您的姓名、电话或邮箱',
    'transparency_standard_4' => '您可以随时停止',
    'transparency_standard_cta' => '开始报告',
    'transparency_standard_learn_more' => '您的数据如何受到保护',

    // V2 Transparency Screen — Conflict mode
    'transparency_conflict_1' => '我们不会收集您的姓名、电话或邮箱',
    'transparency_conflict_2' => '提交后，您的设备上不会保留任何内容',
    'transparency_conflict_3' => '您可以随时关闭此应用',
    'transparency_conflict_4' => '您的报告帮助联合国开发计划署了解您所在区域的情况',
    'transparency_conflict_cta' => '提交报告',
    'transparency_conflict_learn_more' => '这是如何运作的',

    // V2 Recovery Outcome banner
    'recovery_update_from' => '来自 :area 的最新动态：',
    'recovery_contributed' => '您的报告促成了这一成果。',

    // V2 Trusted device
    'trusted_contributor' => '受信任的贡献者',

    // UI — Confirmation screen
    'report_submitted' => '报告已提交',
    'report_submitted_desc' => '您的损害报告已成功记录。',
    'report_id_label' => '报告编号：',
    'submitted_label' => '提交时间：',

    // UI — Engagement panel
    'community_contributions' => '社区贡献',
    'community_contributions_desc' => '每一份报告都帮助救援人员更快地到达需要帮助的人身边。',
    'community_count_label' => '社区成员已提交报告',
    'user_count_label' => '您已提交的报告',
    'achievements' => '成就',
    'earned' => '已获得',
    'locked' => '未解锁',

    // UI — Navigation
    'app_name' => 'RAPIDA',
    'status_online' => '在线',
    'status_offline' => '离线',
    'status_syncing' => ':count 正在同步',
    'safe_exit' => '安全退出',
    'safe_exit_aria' => '安全退出——快速离开此页面',

    // UI — Report detail
    'report_id' => '报告编号',
    'infrastructure' => '基础设施',
    'crisis_type' => '危机类型',
    'reported_by' => '报告人',
    'community_reporter' => '社区报告者',
    'submitted' => '已提交',
    'location' => '位置',
    'version_history' => '版本历史',
    'current_version' => '当前版本',
    'back_to_reports' => '返回我的报告',

    // UI — My Reports
    'my_reports' => '我的报告',
    'my_reports_desc' => '您提交的损害报告。您的数据始终属于您。',
    'synced' => '已同步',
    'pending' => '待处理',
    'no_reports_yet' => '您还没有提交过报告。',
    'submit_new_report' => '提交新报告',

    // UI — Map home
    'report_damage' => '报告损害',
    'recent_reports' => '最新报告',
    'no_reports_community' => '尚无报告提交。',
    'be_first' => '成为第一个帮助社区的人。',

    // UI — Damage levels
    'damage_minimal' => '轻微',
    'damage_partial' => '部分',
    'damage_complete' => '完全',

    // UI — Sync status
    'sync_synced' => '已同步',
    'sync_pending' => '等待同步',
    'sync_failed' => '同步失败',
];
