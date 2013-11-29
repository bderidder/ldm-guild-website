package ladanse.website.domain;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;

@Entity
@Table(name = "Account")
public class Account
{
   private Long id;

   @Id
   @Column(name = "id")
   public Long getId()
   {
	 return id;
   }

   public void setId(Long id)
   {
	 this.id = id;
   }
}
