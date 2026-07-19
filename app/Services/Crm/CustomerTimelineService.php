<?php

namespace App\Services\Crm;

use App\Models\Activity;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Opportunity;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CustomerTimelineService
{
    /**
     * Get aggregated timeline events for a customer model (Lead, Contact, Account).
     *
     * @param Model $model
     * @return Collection
     */
    public function getForModel(Model $model): Collection
    {
        $events = collect();

        // 1. Model Creation Event
        $events->push($this->formatEvent(
            'created',
            $model->created_at,
            class_basename($model) . ' Created',
            "This " . strtolower(class_basename($model)) . " record was created.",
            $model->creator?->name ?? 'System',
            'plus-circle',
            'blue'
        ));

        // Fetch Meetings (if the model has a meetings morphMany relationship)
        if (method_exists($model, 'meetings')) {
            $model->meetings()->with('creator')->get()->each(function ($meeting) use (&$events) {
                $events[] = [
                    'type' => 'meeting',
                    'date' => $meeting->start_at,
                    'title' => 'Meeting Scheduled: ' . $meeting->title,
                    'description' => $meeting->notes ?? $meeting->description ?? 'No details provided.',
                    'user' => $meeting->creator ? $meeting->creator->first_name . ' ' . $meeting->creator->last_name : 'System',
                    'icon' => 'calendar',
                    'color' => 'indigo',
                    'url' => route('admin.meetings.show', $meeting),
                ];
            });
        }

        // 2. Activities (Meetings, Calls, Tasks, etc.)
        if (method_exists($model, 'activities')) {
            $activities = $model->activities()->with(['creator', 'assignee'])->get();
            foreach ($activities as $activity) {
                $icon = match(strtolower($activity->type)) {
                    'meeting' => 'users',
                    'call' => 'phone',
                    'email' => 'mail',
                    'task' => 'check-square',
                    default => 'calendar'
                };
                
                $color = match($activity->status) {
                    'Completed' => 'green',
                    'Pending' => 'yellow',
                    'Cancelled' => 'red',
                    default => 'gray'
                };

                $events->push($this->formatEvent(
                    'activity',
                    $activity->created_at,
                    $activity->type . ': ' . $activity->subject,
                    $activity->description,
                    $activity->creator?->name ?? 'System',
                    $icon,
                    $color
                ));
            }
        }

        // 3. Notes / Discussions (Comments)
        if (method_exists($model, 'comments')) {
            $comments = $model->comments()->with('user')->get();
            foreach ($comments as $comment) {
                $events->push($this->formatEvent(
                    'comment',
                    $comment->created_at,
                    'Note Added',
                    strip_tags($comment->body),
                    $comment->user?->full_name ?? 'System',
                    'message-circle',
                    'yellow'
                ));
            }
        }

        // 4. Documents Uploaded
        if (method_exists($model, 'documents')) {
            $documents = $model->documents()->with('uploader')->get();
            foreach ($documents as $document) {
                $events->push($this->formatEvent(
                    'document',
                    $document->created_at,
                    'Document Uploaded',
                    $document->original_name ?? $document->file_name ?? 'Unknown file',
                    $document->uploader?->first_name ? $document->uploader->first_name . ' ' . $document->uploader->last_name : 'System',
                    'file-text',
                    'indigo',
                    route('admin.documents.show', $document)
                ));
            }
        }

        // 5. Opportunities / Conversions
        // For Leads: convertedOpportunity
        if (method_exists($model, 'convertedOpportunity') && $model->convertedOpportunity) {
            $opp = $model->convertedOpportunity;
            $events->push($this->formatEvent(
                'opportunity',
                $model->converted_at ?? $opp->created_at,
                'Converted to Opportunity',
                "Deal: {$opp->name} | Value: " . format_currency($opp->expected_revenue),
                $model->updater?->name ?? 'System',
                'briefcase',
                'emerald'
            ));
        }

        // For Accounts/Contacts: opportunities
        if (method_exists($model, 'opportunities')) {
            $opportunities = $model->opportunities()->with('creator')->get();
            foreach ($opportunities as $opp) {
                $events->push($this->formatEvent(
                    'opportunity',
                    $opp->created_at,
                    'Opportunity Created',
                    "Deal: {$opp->name} | Value: " . format_currency($opp->expected_revenue),
                    $opp->creator?->name ?? 'System',
                    'briefcase',
                    'emerald'
                ));
            }
        }

        // 6. Workflows / Approvals
        if (method_exists($model, 'approvals')) {
            $approvals = $model->approvals()->with(['workflow', 'submitter'])->get();
            foreach ($approvals as $approval) {
                $color = match($approval->status) {
                    'approved' => 'green',
                    'rejected' => 'red',
                    'pending' => 'yellow',
                    default => 'gray'
                };
                $events->push($this->formatEvent(
                    'approval',
                    $approval->created_at,
                    'Workflow: ' . ($approval->workflow?->name ?? 'Approval'),
                    "Status: " . ucfirst($approval->status),
                    $approval->submitter?->name ?? 'System',
                    'check-circle',
                    $color
                ));
            }
        }

        // Sort by date descending
        return $events->sortByDesc('date')->values();
    }

    /**
     * Helper to format an event into a standardized structure.
     */
    protected function formatEvent(string $type, $date, string $title, ?string $description, string $user, string $icon, string $color, ?string $url = null): array
    {
        return [
            'type' => $type,
            'date' => $date,
            'title' => $title,
            'description' => $description,
            'user' => $user,
            'icon' => $icon,
            'color' => $color,
            'url' => $url,
        ];
    }
}
