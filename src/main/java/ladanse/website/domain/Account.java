package ladanse.website.domain;

import javax.persistence.*;

@Entity
@Table(name = "Account")
public class Account {
    private Long id;

    @Id
    @Column(name = "id")
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }
}
