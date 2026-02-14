<div class = "sidebar-wrapper">

<?php 
// sidebar dashbarod
$user = wp_get_current_user();

if ( in_array( 'player', (array) $user->roles ) ) {
    echo '<p>This is for Players</p>';

} elseif ( in_array( 'coach', (array) $user->roles ) ) { ?>

 <ul>
        <li><a href = "/dashboard/"><i class="fa fa-volume-up"></i> Dashboard</a></li>
        <li><a href = "/my-account/"><i class="fa fa-user-circle" aria-hidden="true"></i> Information</a></li>
        <li><a href = "/update/"><i class="far fa-edit"></i> Profile</a></li>
        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
               <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
            </a>
        </li>   
 </ul>

<?php } elseif ( in_array( 'court', (array) $user->roles ) ) { ?>

 <ul>
        <li><a href = "/dashboard/"><i class="fa fa-volume-up" aria-hidden="true"></i> Dashboard</a></li>
        <li><a href = "/my-account/"><i class="fa fa-user-circle" aria-hidden="true"></i> My Account</a></li>
        <li><a href = "/update/"><i class="far fa-edit"></i>  Update List</a></li>
        <li><a href = "/add-list/"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Your List</a></li>
        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>Logout
            </a>
        </li>   
 </ul>

<?php } ?>


   
</div>