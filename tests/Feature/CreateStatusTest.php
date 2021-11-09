<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateStatusTest extends TestCase

{
use RefreshDatabase;

    public function test_an_guests_users_can_not_create_statuses(){

        $response = $this->post(route('statuses.store'), ['body' => 'My first status']);

        $response->assertRedirect('login');
        
    }


    public function test_an_authenticated_user_can_create_statuses()
    {

       $this->withoutExceptionHandling(); //para que Laravel no maneje las excepciones

        // 1. Given => Teniendo un usuario autenticado
       $user = factory(User::class)->create();
       $this->actingAs($user);

        // 2. When => Hace un post request a status
        $response = $this->post(route('statuses.store'), ['body' => 'My first status']);

        $response->assertJson([
            'body' => 'My first status'
        ]);
        // 3. Then => Veo un nuevo estado en la base de datos
        $this->assertDatabaseHas('statuses', [

            'user_id'   => $user->id,
            'body' => 'My first status'
            
        ]);

     
    }
    public function test_a_status_requires_a_body()
    
    {

         $user = factory(User::class)->create();
         $this->actingAs($user);
 
        
         $response = $this->postJson(route('statuses.store'), ['body' => '']);
       
     
        $response->assertJsonStructure([
            'message', 'errors' => ['body']
            ]);
       
        

         }
    public function test_a_status_body_requires_a_minimum_length()
    
    {  $user = factory(User::class)->create();
       $this->actingAs($user);

       
       $response = $this->postJson(route('statuses.store'), ['body' => 'abcd']);

       $response->assertStatus(422);
      
    
    
         
      
        

        
     }

    
}