<?php

// init
$manageJsonFile = new ManageJsonFile('./employees.json');
// get employees Activated and suscribed this year
$employees = $manageJsonFile->getAllEmployeesActivated();

echo json_encode($employees);

// Class ManageJsonFile
class ManageJsonFile
{
    protected $path;
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    /**
     * Get all Employees
     * @return array<int, Employee>
     */
    public function getAllEmployees(): array
    {
        $aData = $this->getDataFronJsonFile();
        $employees = [];
        foreach ($aData as $value) {
            $employee = new Employee($value->id);
            $profile = new Profile($value->profile->firstName, $value->profile->lastName);
            $employees[] = $employee->setProfile($profile)
              ->setEmail($value->email)
              ->setAddress($value->address)
              ->setRegistered($value->registered)
              ->setIsActive($value->isActive);
        }
        return $employees;
    }

    /**
     * Get all Employees activated
     * 
     */
    public function getAllEmployeesActivated(): array
    {
        //TO DO Manage add option to send year in parameter to get older info

        $employees = array_map(
            function (Employee $employee) {
                $dt = new DateTime($employee->getRegistered());
                // Only Activated Employee and subcribed this year 
                if ($employee->getIsActive() && date("Y") === $dt->format('Y')) {
                    //  'Y-m-d H:i:sP' to have all date in the same format
                    return json_encode([
                        "id" => $employee->getIid(),
                        "profile" => [
                            "firstName" => $employee->getProfile()->getFirstName(),
                            "lastName" => $employee->getProfile()->getLastName(),
                        ],
                        "email"=> $employee->getEmail(),
                        "address"=> $employee->getAddress(),
                        "registered"=> $dt->format('Y-m-d H:i:sP'),
                        "isActive"=> $employee->getIsActive()
                    ]);
                }
            },
            $this->getAllEmployees()
        );

        // remouve null value
        return array_filter($employees);
    }

    /***
     * JSON read from file
     * @return array<int, objetcJson>
     */
    private function getDataFronJsonFile(): ?array
    {
        $data = file_get_contents($this->path);
        return json_decode($data);
    }
}


// Class Profile
class Profile
{
    private $firstName;
    private $lastName;

    /**
     * GETTER
     */
    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * SETTER
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName() : self
    {
        $this->lastName = $lastName;
        return $this;
    }
}

// Class Employee
class Employee
{
    private $id;
    private $profile;
    private $email;
    private $address;
    private $registered;
    private $isActive;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * GETTER
     */
    public function getIid()
    {
        return $this->id;
    }
    public function getProfile()
    {
        return $this->profile;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getRegistered()
    {
        return $this->registered;
    }
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * SETTER
     */
    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;
        return $this;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }
    public function setRegistered(string $registered): self
    {
        $this->registered = $registered;
        return $this;
    }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
?>