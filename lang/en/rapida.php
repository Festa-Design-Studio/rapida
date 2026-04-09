<?php

return [
    // V2 Copy Contracts — Empty states
    'empty_map' => 'No reports yet in this area. Be the first to help your community.',

    // V2 Copy Contracts — Rate limiting
    'rate_limit_building' => "You've already submitted a report for this building today. Thank you for staying engaged.",
    'rate_limit_global' => 'The system is very busy right now. Your report is saved and will send shortly.',

    // V2 Copy Contracts — Photo
    'photo_too_large' => "This photo is too large to send. Try taking a new one — you don't need high quality, just clear enough to see the damage.",
    'photo_guidance_cta_first' => 'Allow camera access',
    'photo_guidance_cta_repeat' => 'Open camera',

    // V2 Copy Contracts — AI classification turn-taking
    'ai_analyzing' => 'Analysing your photo...',
    'ai_suggestion_prompt' => 'We think this is :level. Does that look right?',
    'ai_not_ready' => 'Analysis not ready — please choose the damage level.',

    // V2 Copy Contracts — Confirmation (online vs offline)
    'confirmation_online' => 'Your report was received.',
    'confirmation_offline' => "Your report is saved. It will send automatically when you're connected. You don't need to do anything else.",
    'confirmation_report_id' => 'Report ID: #:id',
    'confirmation_thanks' => 'Thank you for helping your community.',
    'confirmation_joins' => 'Your report joins :count others from :area.',

    // V2 Copy Contracts — Account creation CTA (Hall Lesson 6)
    'account_cta' => 'Get notified when field teams reach your area',

    // V2 Copy Contracts — Conflict mode banner
    'conflict_mode_banner' => 'Anonymous mode. We do not store anything that could identify you.',

    // V2 Transparency Screen — Standard mode
    'transparency_standard_1' => 'Your photo + location go to UNDP emergency teams',
    'transparency_standard_2' => 'Your report appears on a live damage map',
    'transparency_standard_3' => 'No name, phone, or email is ever collected',
    'transparency_standard_4' => 'You can stop at any time',
    'transparency_standard_cta' => 'Begin report',
    'transparency_standard_learn_more' => 'How your data is protected',

    // V2 Transparency Screen — Conflict mode
    'transparency_conflict_1' => 'No name, phone, or email is collected',
    'transparency_conflict_2' => 'Nothing is stored on your device after you submit',
    'transparency_conflict_3' => 'You can close this app at any time',
    'transparency_conflict_4' => 'Your report helps UNDP understand what is happening in your area',
    'transparency_conflict_cta' => 'Submit a report',
    'transparency_conflict_learn_more' => 'How this works',

    // V2 Recovery Outcome banner
    'recovery_update_from' => 'An update from :area:',
    'recovery_contributed' => 'Your reports contributed to this outcome.',

    // V2 Trusted device
    'trusted_contributor' => 'Trusted contributor',

    // UI — Confirmation screen
    'report_submitted' => 'Report Submitted',
    'report_submitted_desc' => 'Your damage report has been recorded successfully.',
    'report_id_label' => 'Report ID:',
    'submitted_label' => 'Submitted:',

    // UI — Engagement panel
    'community_contributions' => 'Community Contributions',
    'community_contributions_desc' => 'Every report helps responders reach those in need faster.',
    'community_count_label' => 'Community members have submitted reports',
    'user_count_label' => 'Reports you have submitted',
    'achievements' => 'Achievements',
    'earned' => 'Earned',
    'locked' => 'Locked',

    // UI — Progress ring
    'progress_ring_title' => 'Zone Coverage',
    'progress_ring_desc' => ':percent% of buildings in your zone have been reported',
    'progress_ring_buildings' => ':reported of :total buildings',

    // UI — Leaderboard
    'leaderboard_title' => 'Top Contributors',
    'leaderboard_reports' => ':count reports',
    'leaderboard_you' => 'You',
    'leaderboard_anonymous' => 'Anonymous reports are not ranked',

    // UI — Navigation
    'app_name' => 'RAPIDA',
    'status_online' => 'Online',
    'status_offline' => 'Offline',
    'status_syncing' => ':count syncing',
    'safe_exit' => 'Safe Exit',
    'safe_exit_aria' => 'Safe exit — quickly leave this page',

    // UI — Report detail
    'report_id' => 'Report ID',
    'infrastructure' => 'Infrastructure',
    'crisis_type' => 'Crisis Type',
    'reported_by' => 'Reported by',
    'community_reporter' => 'Community Reporter',
    'submitted' => 'Submitted',
    'location' => 'Location',
    'version_history' => 'Version History',
    'current_version' => 'Current Version',
    'back_to_reports' => 'Back to My Reports',

    // UI — My Reports
    'my_reports' => 'My Reports',
    'my_reports_desc' => 'Your submitted damage reports. Your data is always yours.',
    'synced' => 'Synced',
    'pending' => 'Pending',
    'no_reports_yet' => "You haven't submitted any reports yet.",
    'submit_new_report' => 'Submit New Report',

    // UI — Dashboard nav
    'dashboard_field' => 'Field Map',
    'dashboard_analyst' => 'Analyst',
    'dashboard_export' => 'Export',

    // UI — Map home
    'report_damage' => 'Report Damage',
    'recent_reports' => 'Recent Reports',
    'no_reports_community' => 'No reports submitted yet.',
    'be_first' => 'Be the first to help your community.',

    // UI — Verification / Redundancy
    'tab_verification' => 'Verification',
    'tab_redundancy' => 'Redundancy',
    'redundancy_dismiss' => 'Dismiss',
    'redundancy_keep' => 'Keep',

    // UI — Analytics panel
    'reports_over_time' => 'Reports Over Time',
    'top_buildings' => 'Most Reported Buildings',
    'building_id' => 'Building',
    'reports_count' => 'Reports',
    'last_updated' => 'Last Updated',

    // UI — Damage levels
    'damage_level_label' => 'Damage Level',
    'damage_minimal' => 'Minimal',
    'damage_partial' => 'Partial',
    'damage_complete' => 'Complete',

    // UI — AI confidence
    'ai_confidence_label' => 'AI Confidence',
    'ai_confidence_high' => 'High confidence',
    'ai_confidence_medium' => 'Medium confidence',
    'ai_confidence_low' => 'Low confidence',
    'ai_suggestion_with_confidence' => 'We think this is :level (:percent confident). Does that look right?',

    // UI — Sync status
    'sync_synced' => 'Synced',
    'sync_pending' => 'Pending sync',
    'sync_failed' => 'Sync failed',
];
