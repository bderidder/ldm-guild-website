package ladanse.website.domain;

import ladanse.website.domain.master.RoleType;
import ladanse.website.domain.master.SignUpType;

import javax.persistence.*;
import java.util.Date;
import java.util.HashSet;
import java.util.Set;

@Entity
@Table(name = "Event")
public class Event
{
   private Long id;
   private String name;
   private Date inviteTime;
   private Date startTime;
   private String description;
   private Set<SignUp> signUps;
   private Account organiser;

   protected Event()
   {
   }

   public Event(Account organiser)
   {
	 setOrganiser(organiser);
   }

   public void signUp(Account account, SignUpType signUpType, Set<RoleType> roleTypes)
   {
	 SignUp signUp = new SignUp(account, this, signUpType, roleTypes);

	 if (signUps == null)
	 {
	    signUps = new HashSet<>();
	 }

	 getSignUps().add(signUp);
   }

   @Id
   @Column(name = "id")
   @GeneratedValue(strategy = GenerationType.IDENTITY)
   public Long getId()
   {
	 return id;
   }

   protected void setId(Long id)
   {
	 this.id = id;
   }

   @Basic
   @Column(name = "name")
   public String getName()
   {
	 return name;
   }

   public void setName(String name)
   {
	 this.name = name;
   }

   @Basic
   @Column(name = "inviteTime")
   @Temporal(TemporalType.TIMESTAMP)
   public Date getInviteTime()
   {
	 return inviteTime;
   }

   public void setInviteTime(Date inviteTime)
   {
	 this.inviteTime = inviteTime;
   }

   @Basic
   @Column(name = "startTime")
   @Temporal(TemporalType.TIMESTAMP)
   public Date getStartTime()
   {
	 return startTime;
   }

   public void setStartTime(Date startTime)
   {
	 this.startTime = startTime;
   }

   @Basic
   @Column(name = "description")
   public String getDescription()
   {
	 return description;
   }

   public void setDescription(String description)
   {
	 this.description = description;
   }

   @OneToMany(cascade = CascadeType.ALL, mappedBy = "event")
   public Set<SignUp> getSignUps()
   {
	 return signUps;
   }

   protected void setSignUps(Set<SignUp> signUps)
   {
	 this.signUps = signUps;
   }

   @ManyToOne
   @JoinColumn(name = "organiser", nullable = false)
   public Account getOrganiser()
   {
	 return organiser;
   }

   protected void setOrganiser(Account organiser)
   {
	 this.organiser = organiser;
   }
}
