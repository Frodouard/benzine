#include<iostream>
using namespace std;
class calculation{
	public:
		int add(int a,int b){
			return a+b;
		}
};
int main()
{
	calculation c;
	cout<<"sum="<<c.add(20,10)<< endl;
	return 0;
}
	
