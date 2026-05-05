<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {   
        $this->call(CountryTableSeeder::class);
        $this->call(RegionTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(UsersTableSeeder::class);     
        $this->call(MenuTableSeeder::class);
        $this->call(MenuUserTableSender::class);         
        $this->call(PrincipalTableSeeder::class); 
        $this->call(LocationStatusTableSeeder::class); 
        $this->call(LocationTypeTableSeeder::class); 
        $this->call(JobClassTableSeeder::class);    
        $this->call(SiteTypeTableSeeder::class);           
        $this->call(SiteIndicatorTableSeeder::class);       
        $this->call(SiteTableSeeder::class);                
        $this->call(SiteAreaTableSeeder::class);          
        $this->call(ModeTableSeeder::class);     
        $this->call(ContainerSizeTableSeeder::class);       
        $this->call(ContainerTypeTableSeeder::class);          
        $this->call(StockStatusTableSeeder::class);
        $this->call(AdjustmentTypeTableSeeder::class);         
        $this->call(CustomerGroupTableSeeder::class);
        $this->call(CustomerTypeTableSeeder::class); 
        $this->call(UnitTableSeeder::class);        
        $this->call(ProductCategoryTableSeeder::class);
        $this->call(ProductGroupTableSeeder::class); 
        $this->call(ProductBrandTableSeeder::class); 
    }
}
