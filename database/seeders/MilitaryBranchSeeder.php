<?php

namespace Database\Seeders;

use App\Models\MilitaryBranch;
use Illuminate\Database\Seeder;

class MilitaryBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $militaryBranches = [
            'Державна прикордонна служба України',
            'Збройні сили України' => [
                'Сухопутні війська Збройних сил України',
                'Повітряні сили Збройних сил України',
                'Військово-морські сили Збройних сил України',
                'Десантно-штурмові війська Збройних сил України',
                'Морська піхота Збройних сил України',
                'Сили спеціальних операцій Збройних сил України',
                'Сили територіальної оборони Збройних сил України',
            ],
            'Національна гвардія України',
            'Національна поліція України',
            'Служба безпеки України',
        ];

        $this->storeMilitaryBranches($militaryBranches);

    }

    private function storeMilitaryBranches(array $branches, ?int $parentId = null): void
    {
        foreach ($branches as $key => $branch) {
            if (is_string($key)) {
                $this->storeMilitaryBranches(
                    $branch,
                    $this->createMilitaryBranch($key, $parentId)
                );

                continue;
            }
            $this->createMilitaryBranch($branch, $parentId);
        }
    }

    private function createMilitaryBranch(string $name, ?int $parentId = null): int
    {
        return MilitaryBranch::create([
            'name' => $name,
            'parent_id' => $parentId,
        ])->id;
    }
}
