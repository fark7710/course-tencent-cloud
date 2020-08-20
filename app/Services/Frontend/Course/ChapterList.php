<?php

namespace App\Services\Frontend\Course;

use App\Caches\CourseCatalog as CourseCatalogCache;
use App\Models\Course as CourseModel;
use App\Models\User as UserModel;
use App\Repos\Course as CourseRepo;
use App\Services\Frontend\CourseTrait;
use App\Services\Frontend\Service as FrontendService;

class ChapterList extends FrontendService
{

    use CourseTrait;

    public function handle($id)
    {
        $course = $this->checkCourse($id);

        $user = $this->getCurrentUser();

        $this->setCourseUser($course, $user);

        return $this->getChapters($course, $user);
    }

    protected function getChapters(CourseModel $course, UserModel $user)
    {
        $cache = new CourseCatalogCache();

        $chapters = $cache->get($course->id);

        if (count($chapters) == 0) {
            return [];
        }

        if ($user->id > 0 && $this->courseUser) {
            $mapping = $this->getLearningMapping($course->id, $user->id, $this->courseUser->plan_id);
            foreach ($chapters as &$chapter) {
                foreach ($chapter['children'] as &$lesson) {
                    $lesson['me'] = [
                        'owned' => $this->ownedCourse || $lesson['free'] ? 1 : 0,
                        'progress' => $mapping[$lesson['id']]['progress'] ?? 0,
                        'duration' => $mapping[$lesson['id']]['duration'] ?? 0,
                    ];
                }
            }
        } else {
            foreach ($chapters as &$chapter) {
                foreach ($chapter['children'] as &$lesson) {
                    $lesson['me'] = [
                        'owned' => $this->ownedCourse || $lesson['free'] ? 1 : 0,
                        'progress' => 0,
                        'duration' => 0,
                    ];
                }
            }
        }

        return $chapters;
    }

    protected function getLearningMapping($courseId, $userId, $planId)
    {
        $courseRepo = new CourseRepo();

        $userLearnings = $courseRepo->findUserLearnings($courseId, $userId, $planId);

        if ($userLearnings->count() == 0) {
            return [];
        }

        $mapping = [];

        foreach ($userLearnings as $learning) {
            $mapping[$learning->chapter_id] = [
                'progress' => $learning->progress,
                'duration' => $learning->duration,
                'consumed' => $learning->consumed,
            ];
        }

        return $mapping;
    }

}
