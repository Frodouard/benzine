#include <iostream>
using namespace std;

class Student
{
public:
    string name;
    int age;

    void read()
    {
        cout << "Enter Name: ";
        cin >> name;

        cout << "Enter Age: ";
        cin >> age;
    }

    void display()
    {
        cout << "Name: " << name << endl;
        cout << "Age: " << age << endl;
    }
};

int main()
{
    Student s[3];   // Array of 3 objects

    for(int i = 0; i < 3; i++)
    {
        cout << "\nStudent " << i + 1 << endl;
        s[i].read();
    }

    cout << "\nStudent Information\n";

    for(int i = 0; i < 3; i++)
    {
        cout << "\nStudent " << i + 1 << endl;
        s[i].display();
    }

    return 0;
}
