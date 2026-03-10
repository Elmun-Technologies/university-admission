<?php

return [
    'adminEmail' => 'admin@university-admission.uz',
    'supportEmail' => 'support@university-admission.uz',
    'senderEmail' => 'noreply@university-admission.uz',
    'senderName' => 'University Admission',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,

    // Runtime Management Parameters
    'maxFileUploadMb' => 5,
    'supportedImageTypes' => ['jpg', 'jpeg', 'png'],
    'studentPhotoMaxWidth' => 400,
    'contractNumberPrefix' => 'UNI',
    'examTimeWarningMinutes' => 5,
    'maxLoginAttempts' => 5,
    'loginLockoutMinutes' => 15,
    'backupRetentionDays' => 30,
];
