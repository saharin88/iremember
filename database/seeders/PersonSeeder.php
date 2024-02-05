<?php

namespace Database\Seeders;

use App\Enums\Citizenship;
use App\Jobs\SavePhoto;
use App\Models\Link;
use App\Models\Location;
use App\Models\MilitaryBranch;
use App\Models\MilitaryPosition;
use App\Models\Person;
use App\Models\Rank;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Revolution\Google\Sheets\Facades\Sheets;

class PersonSeeder extends Seeder
{
    public static string $firstName = 'Прізвище';

    public static string $lastName = "Ім'я";

    public static string $middleName = 'По батькові';

    public static string $callSign = 'Позивний';

    public static string $deathDetails = 'Обставини смерті';

    public static string $dateFormat = 'd-M-y';

    public static string $birth = 'Дата народження (дата)';

    public static string $death = 'Дата смерті (дата)';

    public static string $burial = 'Дата поховання (дата)';

    public static string $wound = 'Дата поранення (дата)';

    public static string $rank = 'Військове звання';

    public static string $militaryPosition = 'Посада';

    public static string $unit = 'Військовий підрозділ';

    public static string $militaryBranch = 'Силова структура';

    public static string $locationSeparator = ',';

    public static string $birthLocation = 'Місце народження';

    public static string $birthLocationKoatuu = 'Місце народження (код КОАТУУ)';

    public static string $birthLocationKatottg = 'Місце народження (код КАТОТТГ)';

    public static string $deathLocation = 'Місце смерті';

    public static string $deathLocationKoatuu = 'Місце смерті (код КОАТУУ)';

    public static string $deathLocationKatottg = 'Місце смерті (код КАТОТТГ)';

    public static string $burialLocation = 'Місце поховання';

    public static string $burialLocationKoatuu = 'Місце поховання (код КОАТУУ)';

    public static string $burialLocationKatottg = 'Місце поховання (код КАТОТТГ)';

    public static string $woundLocation = 'Місце поранення';

    public static string $cemetery = 'Назва кладовища';

    public static string $link = 'Сторінка пам\'яті';

    public static string $photo = 'Фотопортрет';

    public static string $obituary = 'Некролог';

    public static string $citizenship = 'Громадянство';

    public static string $civil = 'Цивільний';

    public function run(): void
    {

        $items = $this->getGoogleSheetItems();

        foreach ($items as $item) {

            $person = Person::create([
                'first_name' => empty($item[self::$firstName]) ? null : $item[self::$firstName],
                'last_name' => empty($item[self::$lastName]) ? null : $item[self::$lastName],
                'middle_name' => empty($item[self::$middleName]) ? null : $item[self::$middleName],
                'call_sign' => empty($item[self::$callSign]) ? null : $item[self::$callSign],
                'birth' => $this->getBirthDate($item),
                'death' => empty($item[self::$death]) ? null : \DateTime::createFromFormat(self::$dateFormat,
                    $item[self::$death]),
                'burial' => empty($item[self::$burial]) ? null : \DateTime::createFromFormat(self::$dateFormat,
                    $item[self::$burial]),
                'wound' => empty($item[self::$wound]) ? null : \DateTime::createFromFormat(self::$dateFormat,
                    $item[self::$wound]),
                'death_details' => empty($item[self::$deathDetails]) ? null : $item[self::$deathDetails],
                'obituary' => empty($item[self::$obituary]) ? null : $item[self::$obituary],
                'citizenship' => empty($item[self::$citizenship]) ? null : Citizenship::tryFrom($item[self::$citizenship]),
                'sex' => ! empty($item[self::$middleName]) && $this->isFemaleName($item[self::$middleName]) ? 0 : 1,
                'civil' => ! empty($item[self::$civil]),
            ]);

            $this->bindRank($person, $item);

            $this->bindMilitaryPosition($person, $item);

            $this->bindUnit($person, $item);

            $this->bindBirthLocation($person, $item);

            $this->bindDeathLocation($person, $item);

            $this->bindBurialLocation($person, $item);

            $this->bindWoundLocation($person, $item);

            $this->bindLinks($person, $item);

            $this->savePhoto($person, $item);

            $person->save();

        }

    }

    protected function getGoogleSheetItems(): array
    {
        $items = [];

        try {
            $sheet = Sheets::spreadsheet(env('GOOGLE_SPREADSHEET', ''))
                ->sheet(env('GOOGLE_SHEET', ''))
                ->get();
            $header = $sheet->pull(0);
            $items = Sheets::collection($header, $sheet)->toArray();
        } catch (\Exception $e) {
            logs()->error($e->getMessage());
        }

        return $items;
    }

    private function getBirthDate(array $item): ?\DateTime
    {
        $birthDate = empty($item[self::$birth]) ? null : \DateTime::createFromFormat('!'.self::$dateFormat, $item[self::$birth]);

        if ($birthDate && $birthDate->format('Y') > date('Y')) {
            $birthDate->modify('-100 years');
        }

        return $birthDate;
    }

    protected function isFemaleName(string $middleName): bool
    {
        return mb_substr($middleName, -3, encoding: 'UTF-8') === 'вна';
    }

    protected function bindRank(Person $person, array $item): void
    {
        if (empty($item[self::$rank])) {
            return;
        }
        $person->rank()->associate(Rank::firstOrCreate([
            'name' => trim($item[self::$rank]),
        ]));

    }

    protected function bindMilitaryPosition(Person $person, array $item): void
    {
        if (empty($item[self::$militaryPosition])) {
            return;
        }
        $person->militaryPosition()->associate(MilitaryPosition::firstOrCreate([
            'name' => trim($item[self::$militaryPosition]),
        ]));
    }

    protected function bindUnit(Person $person, array $item): void
    {
        if (empty($item[self::$unit])) {
            return;
        }
        $unit = Unit::firstOrCreate([
            'name' => trim($item[self::$unit]),
        ]);
        $this->bindUnitWithMilitaryBranch($unit, $item);
        $person->unit()->associate($unit);
    }

    protected function bindUnitWithMilitaryBranch(Unit $unit, array $item): void
    {
        if (empty($item[self::$militaryBranch])) {
            return;
        }
        $unit->militaryBranch()->associate(MilitaryBranch::firstOrCreate([
            'name' => trim($item[self::$militaryBranch]),
        ]));
        $unit->save();
    }

    protected function bindBirthLocation(Person $person, array $item): void
    {
        if (empty($item[self::$birthLocation])) {
            return;
        }

        $location = $this->prepareLocation(
            $item[self::$birthLocation],
            empty($item[self::$birthLocationKoatuu]) ? null : $item[self::$birthLocationKoatuu],
            empty($item[self::$birthLocationKatottg]) ? null : $item[self::$birthLocationKatottg]
        );

        $person->birthLocation()->associate($location);
    }

    protected function bindDeathLocation(Person $person, array $item): void
    {
        if (empty($item[self::$deathLocation])) {
            return;
        }

        $location = $this->prepareLocation(
            $item[self::$deathLocation],
            empty($item[self::$deathLocationKoatuu]) ? null : $item[self::$deathLocationKoatuu],
            empty($item[self::$deathLocationKatottg]) ? null : $item[self::$deathLocationKatottg]
        );

        $person->deathLocation()->associate($location);
    }

    protected function bindBurialLocation(Person $person, array $item): void
    {
        if (empty($item[self::$burialLocation])) {
            return;
        }

        $location = $this->prepareLocation(
            $item[self::$burialLocation],
            empty($item[self::$burialLocationKoatuu]) ? null : $item[self::$burialLocationKoatuu],
            empty($item[self::$burialLocationKatottg]) ? null : $item[self::$burialLocationKatottg]
        );

        if (! empty($item[self::$cemetery])) {
            $location = Location::firstOrCreate([
                'name' => trim($item[self::$cemetery]),
                'parent_id' => $location->id,
            ]);
        }

        $person->burialLocation()->associate($location);
    }

    protected function bindWoundLocation(Person $person, array $item): void
    {
        if (empty($item[self::$woundLocation])) {
            return;
        }
        $location = $this->prepareLocation($item[self::$woundLocation]);
        $person->woundLocation()->associate($location);
    }

    protected function prepareLocation(string $data, ?int $koatuu = null, ?string $katottg = null): Location
    {
        $locations = array_reverse(explode(self::$locationSeparator, $data));
        $parent_id = null;
        foreach ($locations as $locationItem) {
            $location = Location::firstOrCreate([
                'name' => trim($locationItem),
                'parent_id' => $parent_id,
            ]);
            $parent_id = $location->id;
        }
        $location->koatuu = $koatuu;
        $location->katottg = $katottg;
        $location->save();

        return $location;
    }

    protected function bindLinks(Person $person, array $item): void
    {
        if (empty($item[self::$link])) {
            return;
        }
        $person->links()->attach(Link::firstOrCreate([
            'url' => trim($item[self::$link]),
        ]));
    }

    protected function savePhoto(Person $person, array $item): void
    {
        if (
            empty($item[self::$photo])
            && ! filter_var($item[self::$photo], FILTER_VALIDATE_URL)
            && ! @getimagesize($item[self::$photo])
        ) {
            return;
        }
        SavePhoto::dispatch($person, $item[self::$photo]);
    }
}
