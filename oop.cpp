#include<iostream>
using namespace std;
class student{
	public:
	string name;// data member
	int age;// data member
	int id;//data member
};
int main()
{
	student s1;
	s1.name="Frodouard";
	s1.age=22;
	s1.id=101;
	cout<<"name:"<<s1.name<<endl;
	cout<<"age:"<<s1.age<<endl;
	cout<<"id:"<<s1.id<<endl;
	return 0;
}
