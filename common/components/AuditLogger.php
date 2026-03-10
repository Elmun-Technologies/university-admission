<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * AuditLogger records sensitive actions for security and compliance
 */
class AuditLogger extends Component
{
    /**
     * Records an audit log entry
     *
     * @param string $action e.g. 'student.status_changed'
     * @param string $entityType e.g. 'student'
     * @param int $entityId
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    public static function log($action, $entityType, $entityId, $oldValue = null, $newValue = null)
    {
        try {
            $user = Yii::$app->has('user') ? Yii::$app->user : null;
            $request = Yii::$app->has('request') && Yii::$app->request instanceof \yii\web\Request ? Yii::$app->request : null;

            Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
                'user_id' => $user ? $user->id : null,
                'branch_id' => ($user && property_exists($user->identity, 'branch_id')) ? $user->identity->branch_id : null,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_value' => $oldValue ? (is_scalar($oldValue) ? $oldValue : Json::encode($oldValue)) : null,
                'new_value' => $newValue ? (is_scalar($newValue) ? $newValue : Json::encode($newValue)) : null,
                'ip_address' => $request ? $request->userIP : '127.0.0.1',
                'user_agent' => $request ? substr($request->userAgent, 0, 255) : 'CLI',
                'created_at' => time(),
            ])->execute();
        } catch (\Exception $e) {
            Yii::error("Audit Log Failure: " . $e->getMessage());
        }
    }
}
